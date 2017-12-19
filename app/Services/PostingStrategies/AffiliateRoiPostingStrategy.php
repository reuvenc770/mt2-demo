<?php

namespace App\Services\PostingStrategies;

use App\Services\Interfaces\IPostingStrategy;
use App\DataModels\ProcessingRecord;

class AffiliateRoiPostingStrategy implements IPostingStrategy {
    
    public function prepareForPosting(ProcessingRecord $record, $targetId) {
        // The way the Campaigner API works makes it easier to handle there,
        // but we need to conform to the interface
        return [
            'email' => $record->emailAddress,
            'listIds' => [$targetId]
        ]; 
    }

    public function prepareForSuppressionPosting($emailAddress, array $targetIds) {
        return [
            'email' => $emailAddress,
            'listIds' => $targetIds
        ];
    }
}