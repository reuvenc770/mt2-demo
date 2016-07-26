<?php

namespace App\Reports\Interfaces;

interface IFormatStrategy {
    public static function formatFile($row);

    public static function formatFileName($date);
}