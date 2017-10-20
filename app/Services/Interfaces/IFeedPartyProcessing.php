<?php

namespace App\Services\Interfaces;

use App\DataModels\RecordProcessingReportUpdate;

interface IFeedPartyProcessing
{
    public function processPartyData(array $records, RecordProcessingReportUpdate $reportUpdate);
}