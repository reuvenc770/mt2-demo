<?php

namespace App\Services\API;
use DB;

/**
 *  Wrapper around calls to the MT1 mail database
 */

class Mt1DbApi
{
    private $finalId;
    private $divisor = 5;

    public function __construct() {}

    /**
     *  function run(): 
     *  Pulls data from remote source ordered by lastUpdated
     */

    public function getMt1EmailLogs($modulus) {
        
        $count = DB::connection('mt1_data')->select("SELECT COUNT(*) AS total FROM client_record_log");
        $count = (int)$count[0]->total;
        echo "Count:" . $count . PHP_EOL;

        $pull = DB::connection('mt1_data')->select("SELECT
            ID,
            email_user_id, 
            client_id, 
            email_addr,  
            IF(unsubscribe_datetime = '0000-00-00 00:00:00', NULL, unsubscribe_datetime) as unsubscribe_datetime,
            status,
            first_name,
            last_name,
            address,
            address2,
            city,
            state,
            zip,
            country,
            if(dob = '0000-00-00 00:00:00', NULL, dob) as dob,
            gender,
            phone,
            mobile_phone,
            work_phone,
            if(capture_date = '0000-00-00 00:00:00' OR capture_date > CURDATE(), CURDATE(), DATE(capture_date)) as capture_date,
            source_url,
            ip,
            lastUpdated
            FROM 
                client_record_log
            WHERE
                email_user_id % {$this->divisor} = {$modulus}
            ORDER BY 
                ID 
            LIMIT 
                50000");
        
        $len = sizeof($pull);
        
        if ($len > 0) {
            end($pull);
            $last = key($pull);
            $this->finalId = $pull[$last]->ID;
            echo $this->finalId . PHP_EOL;
            
            return $pull;
        }

        return [];
    }

    /**
     *  cleanTable(): when signalled, delete all from the table prior to the set id
     */

    public function cleanTable($modulus) {
        
        return DB::connection('mt1_table_sync')
            ->table('client_record_log')
            ->where('ID', '<=', $this->finalId)
            ->whereRaw("email_id % " . $this->divisor . ' = ' . $modulus)
            ->delete();
    }

    public function getMaxFeedId() {
        $result = DB::connection('mt1_data')
            ->table('user')
            ->orderBy('user_id', 'desc')
            ->take(1)
            ->get()[0]->user_id;

        return (int)$result;
    }

    public function getNewFeeds($feedId) {
        return DB::connection('mt1_data')
            ->table('user')
            ->where('user_id', '>', $feedId)
            ->get();
    }

    public function exportContentServerActions($filename, $startDate) {
        $startDateTime = $startDate . ' 00:00:00';
        $endDateTime = $startDate . ' 23:59:59';
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
                    espUserActionDateTime BETWEEN '$startDateTime' AND '$endDateTime'

                INTO OUTFILE '/data/mysql/tmp/$filename'
                FIELDS TERMINATED BY ','
                OPTIONALLY ENCLOSED BY '`'
                LINES TERMINATED BY '\n'");
    }

    public function moveFile($filename) {

        $handle = config('ssh.servers.mt1_slave_db_server.username');
        $host = config('ssh.servers.mt1_slave_db_server.host');
        $pass = config('ssh.servers.mt1_slave_db_server.password');
        $port = config('ssh.servers.mt1_slave_db_server.port');
        $conn = ssh2_connect($host, $port);
        ssh2_auth_password($conn, $handle, $pass);

        // some risk here, so some validation on the filename:
        if (preg_match('/^\w+\.csv$/', $filename)) {
            $path = storage_path() . '/' . $filename;
            ssh2_scp_recv($conn, "/data/mysql/tmp/$filename", $path);
            ssh2_exec($conn, "rm -f /data/mysql/tmp/$filename"); // may still want a better way to do this
        } 
    }
}
