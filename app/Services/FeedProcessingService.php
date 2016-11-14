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

class FeedProcessingService {
    
    private $validators = [];
    private $suppressors = [];
    private $processor;

    private $emailRepo;
    private $instanceRepo;


    public function __construct(EmailRepo $emailRepo, EmailFeedInstanceRepo $instanceRepo, EmailDomainRepo $mailDomainRepo) {
        $this->emailRepo = $emailRepo;
        $this->instanceRepo = $instanceRepo;
    }


    public function process($records) {
        // Step 1. Get suppression information
        $records = $this->suppress($records);

        // Step 2. Validate & store records
        $validatedRecords = [];

        foreach ($records as $record) {
            if (!$record->isSuppressed) {
                $record = $this->validate($record);
                
                if ($record->valid) {
                    $this->reporting($record);
                    $validatedRecords[] = $record;

                    if ($record->newEmail) {
                        $record->domainId = $this->emailDomainRepo->getIdForName($record->emailAddress);
                        $email = $this->emailRepo->insertNew($record->mapToEmails());
                        $record->emailId = $email->id;
                    }

                    $this->instanceRepo->insertDelayedBatch($record->mapToInstances());
                }

            }
        }

        /**
            What about stuff like domain invalid and all that?
        */

        // cleanup
        $this->instanceRepo->insertStored();
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
            $emails[] = $record['email_address'];
        }

        // Run each suppression check
        foreach($this->suppressors as $suppressor) {
            foreach($suppressor->returnSuppressedEmails($emails) as $supp) {
                $suppressed[$supp->email_address] = true;
            }
        }

        // Update status
        foreach ($records as $record) {
            if ($suppressed[$record->emailAddress]) {
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