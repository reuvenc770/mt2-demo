<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobEntry extends Model
{
    CONST RUNNING = 1;
    CONST SUCCESS = 2;
    CONST FAILED = 3;
    CONST WAITING = 4;
    CONST ONQUEUE = 5;
    CONST SKIPPED = 6;
    protected $guarded = ['id'];
    public $timestamps = false;



    public static function getPrettyStatusNames(){
        return array(
            self::ONQUEUE => "On Queue",
            self::RUNNING => "Running",
            self::SUCCESS => "Successful",
            self::FAILED  => "Failed",
            self::WAITING  => "Waiting",
            self::SKIPPED  => "Lock Skip",
        );
    }

}
