<?php

namespace App\Services;
use App\Services\Interfaces\IValidate;
use App\Services\Interfaces\IFeedPartyProcessing;
use App\Services\Interfaces\IFeedSuppression;
use App\Exceptions\ValidationException;
use App\DataModels\ProcessingRecord;
use App\Repositories\EmailRepo;
use App\Repositories\EmailFeedInstanceRepo;
use App\Repositories\EmailDomainRepo;
use App\Repositories\FeedDateEmailBreakdownRepo;
use App\Services\Interfaces\ISuppressionProcessingStrategy;
use App\Models\InvalidReason;
use App\Repositories\InvalidEmailInstanceRepo;

class FeedProcessingService {
    
    private $validators = [];
    private $suppressors = [];
    private $processor;
    private $suppStrategy;

    private $emailRepo;
    private $instanceRepo;
    private $invalidRepo;


    public function __construct(EmailRepo $emailRepo, 
        EmailFeedInstanceRepo $instanceRepo, 
        EmailDomainRepo $emailDomainRepo, 
        FeedDateEmailBreakdownRepo $statsRepo,
        InvalidEmailInstanceRepo $invalidRepo) {

        $this->emailRepo = $emailRepo;
        $this->instanceRepo = $instanceRepo;
        $this->emailDomainRepo = $emailDomainRepo;
        $this->statsRepo = $statsRepo;
        $this->invalidRepo = $invalidRepo;
    }


    public function process($records) {
        // Step 1. Get suppression information
        $records = $this->suppress($records);

        // Step 2. Validate & store records
        $validatedRecords = [];
        $updateArray = [];

        foreach ($records as $record) {

            // Setting email info for the record
            $emailInfo = $this->emailRepo->getAllInfoForAddress($record->emailAddress);

            if (!$record->newEmail) {
                // Email already exists
                $domainGroupId = $record->domainGroupId;
            }
            elseif (preg_match('/@/', $record->emailAddress)) {
                // Need to set these values for validation
                $record->domainId = $this->emailDomainRepo->getIdForName($record->emailAddress);
                $result = $this->emailDomainRepo->getDomainAndClassInfo($record->emailAddress);
                $domainGroupId = $result->domain_group_id;
                $record->domainGroupId = $domainGroupId;
            }
            
            // Setting up array for the record processing report
            if (!isset($updateArray[$record->feedId])) {
                $updateArray[$record->feedId] = [];
                $updateArray[$record->feedId][$domainGroupId] = [
                    'totalRecords' => 0,
                    'badSourceUrls' => 0,
                    'badIpAddresses' => 0,
                    'otherInvalid' => 0,
                    'suppressed' => 0,
                    'suppressedDomains' => 0,
                    'phoneCount' => 0,
                    'fullPostalCount' => 0,
                    'validRecords' => 0
                ];
            }
            elseif (!isset($updateArray[$record->feedId][$domainGroupId])) {
                $updateArray[$record->feedId][$domainGroupId] = [
                    'totalRecords' => 0,
                    'badSourceUrls' => 0,
                    'badIpAddresses' => 0,
                    'otherInvalid' => 0,
                    'suppressed' => 0,
                    'suppressedDomains' => 0,
                    'phoneCount' => 0,
                    'fullPostalCount' => 0,
                    'validRecords' => 0
                ];
            }
            
            $updateArray[$record->feedId][$domainGroupId]['totalRecords']++;

            // Process records and update reporting
            if (!$record->isSuppressed) {
                $record = $this->validate($record);

                if ($record->valid) {

                    if ($record->newEmail) {
                        // Didn't exist at record list generation time and not suppressed (yet)
                        // We might run into issues due to the separate processing of data from feeds of different parties
                        $record->newEmail = true;
                        $email = $this->emailRepo->insertNew($record->mapToEmails());

                        $record->emailId = $email->id;
                    }

                    $validatedRecords[] = $record;
                    $updateArray[$record->feedId][$domainGroupId]['validRecords']++;

                    if ($record->phone) {
                        $updateArray[$record->feedId][$domainGroupId]['phoneCount']++;
                    }

                    if ('' !== $record->address && '' !== $record->zip && '' !== $record->city && '' !== $record->state) {
                        $updateArray[$record->feedId][$domainGroupId]['fullPostalCount']++;
                    }

                    $this->instanceRepo->batchInsert($record->mapToInstances());
                }
                else {
                    $invalidReasonId = null;
                    if (preg_match('/Canad/', $record->invalidReason)) {
                        $updateArray[$record->feedId][$domainGroupId]['otherInvalid']++;
                        $invalidReasonId = InvalidReason::CANADA;
                    }
                    elseif (preg_match('/Email/', $record->invalidReason)) {
                        $updateArray[$record->feedId][$domainGroupId]['otherInvalid']++;
                        $invalidReasonId = InvalidReason::EMAIL;
                    }
                    elseif(preg_match('/Source\surl/', $record->invalidReason)) {
                        $updateArray[$record->feedId][$domainGroupId]['badSourceUrls']++;
                        $invalidReasonId = InvalidReason::BAD_SOURCE_URL;
                    }
                    elseif(preg_match('/IP/', $record->invalidReason)) {
                        $updateArray[$record->feedId][$domainGroupId]['badIpAddresses']++;
                        $invalidReasonId = InvalidReason::BAD_IP_ADDRESS;
                    }
                    elseif(preg_match('/domain/', $record->invalidReason)) {
                        $updateArray[$record->feedId][$domainGroupId]['suppressedDomains']++;
                        $invalidReasonId = InvalidReason::BAD_DOMAIN;
                    }
                    else {
                        $updateArray[$record->feedId][$domainGroupId]['otherInvalid']++;
                        $invalidReasonId = InvalidReason::OTHER_INVALIDATION;
                    }

                    $invalidData = $record->mapToInstances();
                    $invalidData['email_address'] = $record->emailAddress;
                    $invalidData['invalid_reason_id'] = $invalidReasonId;
                    $invalidData['pw'] = '';
                    $invalidData['posting_string'] = '';
                    $this->invalidRepo->batchInsert($invalidData);
                }

            }
            else {
                $updateArray[$record->feedId][$domainGroupId]['suppressed']++;
            }
        }

        // cleanup
        $this->instanceRepo->insertStored();
        $this->invalidRepo->insertStored();

        // Insert into report repo
        $this->statsRepo->updateExtendedStatuses($updateArray);

        $records = []; 

        // Step 3. Process records
        $this->postProcessing($validatedRecords);
    }


    public function registerValidator(IValidate $validator) {
        $this->validators[] = $validator;
        return $this; // reurns $this to enable chaining
    }


    public function registerSuppression(IFeedSuppression $service) {
        $this->suppressors[] = $service;
        return $this;
    }


    public function registerProcessing(IFeedPartyProcessing $service) {
        $this->processor = $service;
    }

    public function setSuppressionProcessingStrategy(ISuppressionProcessingStrategy $suppStrategy) {
        $this->suppStrategy = $suppStrategy;
    }


    private function validate($record) {
        try {
            foreach ($this->validators as $validator) {

                $data = [];

                foreach($validator->getRequiredData() as $field) {
                    $data[$field] = $record->$field;
                }

                $validator->setData($data);
                $validator->validate();

                foreach ($validator->returnData() as $key => $value) {
                    $record->$key = $value;
                }
            }
            $record->valid = true;
        }
        catch (ValidationException $e) {
            $record->valid = false;
            $record->invalidReason = $e->getMessage();
        }

        return $record;
    }

    /**
     *  Set suppression status.
     *  This is obviously a bit more complicated than the simple per-item lookup,
     *  but hopefully this is significantly faster
     */

    private function suppress($records) {

        $emails = [];
        $suppressed = [];
        $finalRecords = [];

        // Build out list of email addresses to check
        foreach($records as $record) {
            $emails[] = $record->emailAddress;
        }

        // Run each suppression check
        foreach($this->suppressors as $suppressor) {
            foreach($suppressor->returnSuppressedEmails($emails) as $supp) {
                $suppressed[$supp->email_address] = true;
                $this->suppStrategy->processSuppression($supp->email_address);
            }
        }

        // Update status
        foreach ($records as $record) {
            if (isset($suppressed[$record->emailAddress])) {
                $record->isSuppressed = true;
            }
            else {
                $record->isSuppressed = false;
            }
            $finalRecords[] = $record;
        }

        return $finalRecords;
    }


    private function postProcessing(array $records) {
        $this->processor->processPartyData($records);
    }
}