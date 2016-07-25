<?php

namespace App\Reports\Strategies;

use App\Reports\Interfaces\IFormatStrategy;

class SprintFormatStrategy implements IFormatStrategy {
    
    public static function formatFile($row) {
        return ['A', $row['email_address'], $row['date'] . ' 00:00:00', 'SPRGPROM', 'ZETA', 'BATCH', 'E'];
    }

    public static function formatFileName($date) {
        return "Zeta_DNE_{$date}.txt";
    }
}