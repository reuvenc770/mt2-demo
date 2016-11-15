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

class FeedProcessingService {
    
    private $validators = [];
    private $suppressors = [];
    private $processor;

    private $emailRepo;
    private $instanceRepo;


    public function __construct(EmailRepo $emailRepo, 
        EmailFeedInstanceRepo $instanceRepo, 
        EmailDomainRepo $emailDomainRepo, 
        FeedDateEmailBreakdownRepo $statsRepo) {

        $this->emailRepo = $emailRepo;
        $this->instanceRepo = $instanceRepo;
        $this->emailDomainRepo = $emailDomainRepo;
        $this->statsRepo = $statsRepo;
    }


    public function process($records) {
        // Step 1. Get suppression information
        $records = $this->suppress($records);

        // Step 2. Validate & store records
        $validatedRecords = [];
        $updateArray = [];

        foreach ($records as $record) {

            $domainGroupId = $record->domainGroupId;

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

            // Process records and update reporting
            if (!$record->isSuppressed) {
                $record = $this->validate($record);

                if ($record->valid) {

                    $validatedRecords[] = $record;
                    $updateArray[$record->feedId][$domainGroupId]['validRecords']++;

                    if ($record->newEmail) {
                        $record->domainGroupId = $this->emailDomainRepo->getIdForName($record->emailAddress);
                        $email = $this->emailRepo->insertNew($record->mapToEmails());

                        $record->emailId = $email->id;
                    }

                    if ($record->phone) {
                        $updateArray[$record->feedId][$domainGroupId]['phoneCount']++;
                    }

                    if ($record->address) {
                        $updateArray[$record->feedId][$domainGroupId]['fullPostalCount']++;
                    }

                    $this->instanceRepo->insertDelayedBatch($record->mapToInstances());
                }
                elseif(preg_match('/source\surl/', $record->invalidReason)) {
                    $updateArray[$record->feedId][$domainGroupId]['badSourceUrls']++;
                }
                elseif(preg_match('/IP/', $record->invalidReason)) {
                    $updateArray[$record->feedId][$domainGroupId]['badIpAddresses']++;
                }
                elseif(preg_match('/domain/', $record->invalidReason)) {
                    $updateArray[$record->feedId][$domainGroupId]['suppressedDomains']++;
                }
                else {
                    $updateArray[$record->feedId][$domainGroupId]['otherInvalid']++;
                }

            }
            else {
                $updateArray[$record->feedId][$domainGroupId]['suppressed']++;
            }
        }

        // cleanup
        $this->instanceRepo->insertStored();

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