<?php

namespace App\Services\Interfaces;

interface IPostingStrategy
{
    public function prepareForPosting(array $records, $targetId);
}