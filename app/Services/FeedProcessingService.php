<?php

namespace App\Services;
use App\Services\Interfaces\IValidate;
use App\Services\Interfaces\IFeedPartyProcessing;
use App\Services\Interfaces\IFeedSuppression;
use App\Exceptions\ValidationException;

class FeedProcessingService {
    
    private $validators = [];
    private $suppressors = [];
    private $processor;

    public function __construct() {}


    public function process($records) {
        // Insert all found records somewhere (?) - or is that handled by another job
        // Then insert into email-feed-instances

        // let's get nonsuppressed records first

        $validatedRecords = [];
        $invalidRecords = [];

        $records = $this->returnUnsuppressedRecords($records);

        /**
            should we deal with suppressed records?
            > Yes, we have to
            Rename method above
        */

        foreach ($records as $record) {
            $record = $this->validate($record);
            $this->reporting($record);
        }

        /**
            Somewhere in here we need to insert into the db:
            emails
            email feed instances
        */

        $this->postSuppressionProcessing($records);
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

            $record->valid = true; //?
            
        }
        catch (ValidationException $e) {
            // Handle exception here

            return;
        }

        $record->valid = false; //?
        return $record;
    }

    private function suppress($records) {
/**
This needs to be modified to keep suppressed records
*/
        $emails = [];
        $emailMatchHash = [];
        $suppressedEmails = [];
        $unsuppressedRecords = [];

        // Build out list of email addresses to check
        // Build out hash map to allow for efficiently pickup up which records should be passed forward
        foreach($records as $record) {
            $emails[] = $record['email_address'];
            // Can't assume that email address is unique in this batch
            $emailMatchHash[$record['email_address']][] = $record; 
        }

        // Run each suppression check
        foreach($this->suppressors as $suppressor) {
            foreach($suppressor->returnSuppressedEmails($emails) as $supp) {
                $suppressedEmails[] = $supp->email_address;
            }
        }

        // Remove emails that were suppressed
        foreach($suppressedEmails as $supp) {
            unset($emailMatchHash[$supp]);
        }

        // Re-hydrate the list of $records based off of the remaining $emails
        // Each email can be held in multiple records
        foreach($emailMatchHash as $key => $values) {
            foreach ($values as $email) {
                $unsuppressedRecords[] = $email;
            }
        }

        return $unsuppressedRecords;
    }


    private function postSuppressionProcessing(array $records) {
        $this->processor->processPartyData($records);
    }


}