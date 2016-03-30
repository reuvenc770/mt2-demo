<?php

namespace App\Services\API;
use DB;

/**
 *  Wrapper around calls to the MT1 mail database
 */

class Mt1DbApi
{
    private $finalLastUpdated;

    public function __construct() {}

    /**
     *  function run(): 
     *  Pulls data from remote source ordered by lastUpdated
     */

    public function getMt1EmailLogs() {
        
        $count = DB::connection('mt1_data')->select("SELECT COUNT(*) AS total FROM client_record_log");
        $count = (int)$count[0]->total;
        echo "Count:" . $count . PHP_EOL;

        $pull = DB::connection('mt1_data')->select("SELECT * FROM client_record_log ORDER BY lastUpdated LIMIT 50000");

        $len = sizeof($pull);
        end($pull);
        $last = key($pull);
        $this->finalLastUpdated = $pull[$last]->lastUpdated;

        echo $this->finalLastUpdated . PHP_EOL;
        
        
        return $pull;
    }

    /**
     *  cleanTable(): when signalled, delete all from the table prior to the set date
     */

    public function cleanTable() {
        
        DB::connection('mt1_table_sync')
            ->table('client_record_log')
            ->where('lastUpdated', '<=', $this->finalLastUpdated)
            ->delete();
        
    }

    public function getMaxClientId() {
        $result = DB::connection('mt1_data')
            ->table('user')
            ->orderBy('user_id', 'desc')
            ->take(1)
            ->get()[0]->user_id;

        return (int)$result;
    }

    public function getNewClients($clientId) {
        return DB::connection('mt1_data')
            ->table('user')
            ->where('user_id', '>', $clientId)
            ->get();
    }
}