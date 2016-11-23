<?php

namespace App\Services\Interfaces;

interface IFeedSuppression
{
    public function returnSuppressedEmails(array $emails);
}