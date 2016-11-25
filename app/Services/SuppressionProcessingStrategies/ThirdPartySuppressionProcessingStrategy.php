<?php

namespace App\Services\SuppressionProcessingStrategies;

use App\Services\Interfaces\ISuppressionProcessingStrategy;

class ThirdPartySuppressionProcessingStrategy implements ISuppressionProcessingStrategy {

    public function __construct() {}

    public function processSuppression($supp) {
        // does nothing
    }
}