<?php

namespace App\Services\PostingStrategies;

use App\Services\Interfaces\IPostingStrategy;
use App\DataModels\ProcessingRecord;

class RmpPostingStrategy implements IPostingStrategy {
    
    public function prepareForPosting(ProcessingRecord $record, $targetId) {
        $field1 = array('fieldId' => '0bce03e9000000000000000000000002a6fb', 'content' => $record->emailId);
        $field2 = array('fieldId' => '0bce03e9000000000000000000000002a70d', 'content' => $record->firstName);

        return [
            'email' => $record->emailAddress,
            'listIds' => $targetId,
            'fields' => [$field1, $field2]
        ];
    }

    public function prepareForSuppressionPosting($emailAddress, array $targetIds) {
        return [
            'email' => $emailAddress,
            'listIds' => $targetIds
        ];
    }
}