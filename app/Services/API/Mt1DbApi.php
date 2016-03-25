<?php

namespace App\Services\API;
use DB;

/**
 *  Wrapper around calls to the MT1 mail database
 */

class Mt1DbApi
{

    public function __construct() {}

    public function getMt1EmailLogs() {
        return DB::connection('mt1mail')->select("SELECT * FROM client_record_log");
    }
}