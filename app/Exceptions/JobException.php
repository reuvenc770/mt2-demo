<?php
namespace App\Exceptions;

use Exception;

class JobException extends Exception {
    CONST NOTICE = 1;
    CONST WARNING = 2;
    CONST ERROR = 3;
    CONST CRITICAL = 4;

    public $delay = 60;

    public function setDelay ( $delayInSeconds ) {
        $this->delay = $delayInSeconds;
    }

    public function getDelay () {
        return $this->delay;
    }
}
