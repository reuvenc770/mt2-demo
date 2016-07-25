<?php

namespace App\Reports\Strategies;

use App\Reports\Interfaces\IFormatStrategy;

class JustEmailFormatStrategy implements IFormatStrategy {
    
    public static function formatFile($row) {
        return [$row['email_address']];
    }

    public static function formatFileName($date) {
        return "esurance_unsubs_{$date}.txt";
    }
}