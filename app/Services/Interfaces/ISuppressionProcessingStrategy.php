<?php

namespace App\Services\Interfaces;

interface ISuppressionProcessingStrategy
{
    public function processSuppression($supp);
}