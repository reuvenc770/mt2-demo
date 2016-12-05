<?php

namespace App\Services\PostingStrategies;

use App\Services\Interfaces\IPostingStrategy;

class SimplyJobsPostingStrategy implements IPostingStrategy {
    
    public function prepareForPosting(array $records, $targetId) {
        $output = [];

        foreach($records as $record) {
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
            $fields[] = array('fieldId' => '0bce03e9000000000000000000000002a97a', 'content' => $record->otherFields['ts']);

            // custom field - job type
            $fields[] = array('fieldId' => '0bce03e9000000000000000000000002a97b', 'content' => $record->otherFields['job_type']);

            // custom field - grad year
            $fields[] = array('fieldId' => '0bce03e9000000000000000000000002a97c', 'content' => $record->otherFields['gradyear']);

            // custom field - education level
            $fields[] = array('fieldId' => '0bce03e9000000000000000000000002a97e', 'content' => $record->otherFields['education_level']);

            // custom field - utm_source
            $fields[] = array('fieldId' => '0bce03e9000000000000000000000002a97f', 'content' => $record->otherFields['utm_source']);

            // custom field - password
            $fields[] = array('fieldId' => '0bce03e9000000000000000000000002a982', 'content' => $record->otherFields['password']);

            // custom field - salary
            $fields[] = array('fieldId' => '0bce03e9000000000000000000000002a97d', 'content' => $record->otherFields['salary']);

            // default value - job alerts
            $fields[] = array('fieldId' => '0bce03e9000000000000000000000002a989', 'content' => true);

            // default value - additional marketing emails
            $fields[] = array('fieldId' => '0bce03e9000000000000000000000002a98a', 'content' => true);

            $output[] = [
                'email' => $record->emailAddress,
                'listIds' => $targetId,
                'fields' => $fields
            ];
        }


        return $output;
    }
}