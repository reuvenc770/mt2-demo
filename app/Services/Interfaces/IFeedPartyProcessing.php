<?php

namespace App\Services\Interfaces;

interface IFeedPartyProcessing
{
    public function processPartyData(array $records);
}