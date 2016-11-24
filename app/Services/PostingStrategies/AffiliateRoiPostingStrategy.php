<?php

namespace App\Services\PostingStrategies;

use App\Services\Interfaces\IPostingStrategy;

class AffiliateRoiPostingStrategy implements IPostingStrategy {
    
    public function prepareForPosting(array $records, $targetId) {
        // The way the Campaigner API works makes it easier to handle there 
        return $records; 
    }
}