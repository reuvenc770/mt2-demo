<?php

namespace App\Services\Interfaces;

use App\DataModels\ProcessingRecord;

interface IPostingStrategy
{
    public function prepareForPosting(ProcessingRecord $record, $targetId);

    public function prepareForSuppressionPosting($emailAddress, array $targetIds);
}