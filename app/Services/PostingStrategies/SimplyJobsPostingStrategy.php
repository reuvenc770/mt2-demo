<?php

namespace App\Services\PostingStrategies;

use App\Services\Interfaces\IPostingStrategy;
use App\DataModels\ProcessingRecord;

class SimplyJobsPostingStrategy implements IPostingStrategy {
    
    public function prepareForPosting(ProcessingRecord $record, $targetId) {
        $fields = [];

        // first name
        $fields[] = array('fieldId' => '0bce03e9000000000000000000000002a974', 'content' => $record->firstName);

        // last name
        $fields[] = array('fieldId' => '0bce03e9000000000000000000000002a975', 'content' => $record->lastName);

        // city
        $fields[] = array('fieldId' => '0bce03e9000000000000000000000002a976', 'content' => $record->city);

        // state
        $fields[] = array('fieldId' => '0bce03e9000000000000000000000002a981', 'content' => $record->state);

        // zip
        $fields[] = array('fieldId' => '0bce03e9000000000000000000000002a978', 'content' => $record->zip);

        // ip
        $fields[] = array('fieldId' => '0bce03e9000000000000000000000002a979', 'content' => $record->ip);

        // custom field - ts
        $fields[] = array('fieldId' => '0bce03e9000000000000000000000002a97a', 'content' => $record->getOtherFields('ts'));

        // custom field - job type
        $fields[] = array('fieldId' => '0bce03e9000000000000000000000002a97b', 'content' => $record->getOtherFields('job_type'));

        // custom field - grad year
        $fields[] = array('fieldId' => '0bce03e9000000000000000000000002a97c', 'content' => $record->getOtherFields('gradyear'));

        // custom field - education level
        $fields[] = array('fieldId' => '0bce03e9000000000000000000000002a97e', 'content' => $record->getOtherFields('education_level'));

        // custom field - utm_source
        $fields[] = array('fieldId' => '0bce03e9000000000000000000000002a97f', 'content' => $record->getOtherFields('utm_source'));

        // custom field - password
        $fields[] = array('fieldId' => '0bce03e9000000000000000000000002a982', 'content' => $record->getOtherFields('password'));

        // custom field - salary
        $fields[] = array('fieldId' => '0bce03e9000000000000000000000002a97d', 'content' => $record->getOtherFields('salary'));

        // default value - job alerts
        $fields[] = array('fieldId' => '0bce03e9000000000000000000000002a989', 'content' => true);

        // default value - additional marketing emails
        $fields[] = array('fieldId' => '0bce03e9000000000000000000000002a98a', 'content' => true);

        return [
            'email' => $record->emailAddress,
            'listIds' => $targetId,
            'fields' => $fields
        ];
    }

    public function prepareForSuppressionPosting($emailAddress, array $targetIds) {
        return [
            'email' => $emailAddress,
            'listIds' => $targetIds
        ];
    }
}