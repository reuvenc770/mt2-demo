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
        DB::connection('mt1_table_sync')->unprepared('LOCK TABLES client_record_log WRITE');
        $output = DB::connection('mt1_data')->select("SELECT * FROM client_record_log LIMIT 10");
        #DB::connection('mt1_table_sync')->table('client_record_log')->truncate();
        DB::connection('mt1_table_sync')->unprepared('UNLOCK TABLES');
        return $output;
    }
}