<?php

namespace App\Services\PostingStrategies;

use App\Services\Interfaces\IPostingStrategy;

class RmpPostingStrategy implements IPostingStrategy {
    
    public function prepareForPosting(array $records, $targetId) {
        $output = [];
        foreach ($records as $record) {
            $field1 = array('fieldId' => '0bce03e9000000000000000000000002a6fb', 'content' => $record->emailId);
            $field2 = array('fieldId' => '0bce03e9000000000000000000000002a70d', 'content' => $record->firstName);

            $output[] = [
                'email' => $record->emailAddress,
                'listIds' => $targetId,
                'fields' => [$field1, $field2]
            ];
        }

        return $output;
    }
}