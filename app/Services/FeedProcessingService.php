<?php

namespace App\Services;
use App\Services\Interfaces\IValidate;
use App\Services\Interfaces\IFeedPartyProcessing;
use App\Services\Interfaces\IFeedSuppression;
use App\Exceptions\ValidationException;
use App\DataModels\ProcessingRecord;
use App\Services\EmailService;
use App\Repositories\EmailFeedInstanceRepo;
use App\Services\EmailDomainService;
use App\Services\Interfaces\ISuppressionProcessingStrategy;
use App\Models\InvalidReason;
use App\Repositories\InvalidEmailInstanceRepo;
use Log;
use App\DataModels\RecordProcessingReportUpdate;

class FeedProcessingService {
    
    private $validators = [];
    private $suppressors = [];
    private $maxId = 0;
    private $processor;
    private $suppStrategy;
    private $reportUpdate;

    private $emailService;
    private $instanceRepo;
    private $invalidRepo;
    private $emailDomainService;


    public function __construct(EmailService $emailService, 
        EmailFeedInstanceRepo $instanceRepo, 
        EmailDomainService $emailDomainService, 
        InvalidEmailInstanceRepo $invalidRepo,
        RecordProcessingReportUpdate $reportUpdate) {

        $this->emailService = $emailService;
        $this->instanceRepo = $instanceRepo;
        $this->emailDomainService = $emailDomainService;
        $this->invalidRepo = $invalidRepo;
        $this->reportUpdate = $reportUpdate;
    }

    public function process($records) {
        $validatedRecords = [];

        foreach($records as $record) {
            $record = $this->emailDomainService->setRecordDomainInfo($record);

            // Setting up the report update object
            $this->reportUpdate->setFields($record);
            $this->reportUpdate->incrementTotal($record);

            // Process records and update reporting
            if (!$record->isSuppressed) {
                $record = $this->validate($record);

                if ($record->valid) {

                    if ($record->newEmail) {
                        // Didn't exist at record list generation time and not suppressed (yet)
                        // We might run into issues due to the separate processing of data from feeds of different parties
                        $record = $this->emailService->createFromRecord($record);
                    }

                    $validatedRecords[] = $record;
                    $this->reportUpdate->incrementValid($record);
                    $this->instanceRepo->batchInsert($record->mapToInstances());
                }
                else {
                    Log::info($record->emailAddress . ' failed validation due to ' . $record->invalidReason);
                    $invalidReasonId = $this->getInvalidReason($record);
                    $this->reportUpdate->incrementInvalid($record, $invalidReasonId);

                    $invalidData = $record->mapToInstances();
                    $invalidData['email_address'] = $record->emailAddress;
                    $invalidData['invalid_reason_id'] = $invalidReasonId;
                    $invalidData['pw'] = '';
                    $invalidData['posting_string'] = '';

                    $this->invalidRepo->batchInsert($invalidData);
                }

            }
            else {
                $this->reportUpdate->incrementSuppressed($record);
            }
        }

        // cleanup
        $this->instanceRepo->insertStored();
        $this->invalidRepo->insertStored();

        // Party-specific processing
        $this->postProcessing($validatedRecords, $this->reportUpdate);
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


    public function validate(ProcessingRecord $record) {
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
     *  Assumes an array of ProcessingRecords
     */

    public function suppress($records) {

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
                $suppressed[strtolower($supp->email_address)] = true;
                $this->suppStrategy->processSuppression($supp->email_address);
            }
        }

        // Update status
        foreach ($records as $record) {
            if (isset($suppressed[strtolower($record->emailAddress)])) {
                $record->isSuppressed = true;
            }
            else {
                $record->isSuppressed = false;
            }
            $finalRecords[] = $record;
        }

        return $finalRecords;
    }


    public function postProcessing(array $records, RecordProcessingReportUpdate $reportUpdate) {
        $this->processor->processPartyData($records, $reportUpdate);
    }


    private function getInvalidReason(ProcessingRecord $record) {
        if (null === $record->invalidReason || '' === $record->invalidReason) {
            return null;
        }

        if (preg_match('/Canad/', $record->invalidReason)) {
            return InvalidReason::CANADA;
        }
        elseif (preg_match('/Email/', $record->invalidReason)) {
            return InvalidReason::EMAIL;
        }
        elseif(preg_match('/Source\surl/', $record->invalidReason)) {
            return InvalidReason::BAD_SOURCE_URL;
        }
        elseif(preg_match('/IP/', $record->invalidReason)) {
            return InvalidReason::BAD_IP_ADDRESS;
        }
        elseif(preg_match('/domain/', $record->invalidReason)) {
            return InvalidReason::BAD_DOMAIN;
        }
        else {
            return InvalidReason::OTHER_INVALIDATION;
        }
    }
}