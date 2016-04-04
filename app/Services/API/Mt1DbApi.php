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

    public function exportContentServerActions($filename) {
        return DB::connection('mt1_data')
            ->statement("SELECT 
                    eua.emailUserId AS email_id,
                    eua.subaffiliateID AS sub_id,
                    euat.emailUserActionLabel AS action_type,
                    IFNULL(eaj.sendDate, '') AS send_date,
                    eua.espUserActionDateTime AS action_time

                FROM
                    EspUserAction eua
                    INNER JOIN EmailUserActionType euat ON eua.espActionTypeID = euat.emailUserActionTypeID
                    INNER JOIN EspAdvertiserJoin eaj ON eua.subaffiliateID = eaj.subAffiliateID
                WHERE
                    espUserActionDateTime BETWEEN CURDATE() - INTERVAL 1 DAY AND CURDATE()

                INTO OUTFILE '/tmp/$filename'
                FIELDS TERMINATED BY ','
                OPTIONALLY ENCLOSED BY '`'
                LINES TERMINATED BY '\n'");
    }

    public function moveFile($filename) {
        
        $handle = env('MT1_SLAVE_DB3_USER', '');
        $host = env('MT1_SLAVE_DB3_HOST', '');
        $pass = env('MT1_SLAVE_DB3_PASS', '');
        $port = env('MT1_SLAVE_DB3_PORT', '');
        $conn = ssh2_connect($host, $port);
        ssh2_auth_password($conn, $handle, $pass);

        // some risk here, so some validation on the filename:
        if (preg_match('/^\w+\.csv$/', $filename)) {
            $path = storage_path() . '/' . $filename;
            ssh2_scp_recv($conn, "/tmp/$filename", $path);
            ssh2_exec($conn, "rm -f /tmp/$filename"); // may still want a better way to do this
        } 
    }
}