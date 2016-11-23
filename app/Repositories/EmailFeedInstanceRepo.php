<?php

namespace App\Repositories;

use App\Models\EmailFeedInstance;
use DB;
use Illuminate\Database\Query\Builder;

/**
 *
 */
class EmailFeedInstanceRepo {

    private $emailFeedModel;
    private $batchInstances = [];
    private $batchInstanceCount = 0;
    const INSERT_THRESHOLD = 10000;

    public function __construct(EmailFeedInstance $emailFeedModel) {
        $this->emailFeedModel = $emailFeedModel;
    }

    public function insertDelayedBatch($row) {
        if ($this->batchInstanceCount >= self::INSERT_THRESHOLD) {

            // Doing this (switching type signature) to save on some memory, given that these lists can be rather large
            $this->batchInstances = implode(', ', $this->batchInstances);

            DB::statement(
                "INSERT INTO email_feed_instances
                (email_id, feed_id, subscribe_datetime, unsubscribe_datetime,
                status, first_name, last_name, address, address2, city, state, 
                zip, country, dob, gender, phone, mobile_phone, work_phone, 
                capture_date, source_url, ip )

                VALUES

                {$this->batchInstances}

                ON DUPLICATE KEY UPDATE
                email_id = email_id,
                feed_id = feed_id,
                subscribe_datetime = subscribe_datetime,
                unsubscribe_datetime = unsubscribe_datetime,
                status = status,
                first_name = first_name,
                last_name = last_name,
                address = address,
                address2 = address2,
                city = city,
                state = state,
                zip = zip,
                country = country,
                dob = dob,
                gender = gender,
                phone = phone,
                mobile_phone = mobile_phone,
                work_phone = work_phone,
                capture_date = capture_date,
                source_url = source_url,
                ip = ip"
            );


            $this->batchInstances = [$this->transformRowToString($row)];
            $this->batchInstanceCount = 1;
        }
        else {
            $this->batchInstances[] = $this->transformRowToString($row);
            $this->batchInstanceCount++;
        }
    }

    public function insertStored() {
        if ($this->batchInstanceCount > 0) {
            $this->batchInstances = implode(', ', $this->batchInstances);

            DB::statement(
                "INSERT INTO email_feed_instances
                (email_id, feed_id, subscribe_datetime, unsubscribe_datetime,
                status, first_name, last_name, address, address2, city, state, 
                zip, country, dob, gender, phone, mobile_phone, work_phone, 
                capture_date, source_url, ip )

                VALUES

                {$this->batchInstances}

                ON DUPLICATE KEY UPDATE
                email_id = email_id,
                feed_id = feed_id,
                subscribe_datetime = subscribe_datetime,
                unsubscribe_datetime = unsubscribe_datetime,
                status = status,
                first_name = first_name,
                last_name = last_name,
                address = address,
                address2 = address2,
                city = city,
                state = state,
                zip = zip,
                country = country,
                dob = dob,
                gender = gender,
                phone = phone,
                mobile_phone = mobile_phone,
                work_phone = work_phone,
                capture_date = capture_date,
                source_url = source_url,
                ip = ip"
            );

            $this->batchInstances = [];
            $this->batchInstanceCount = 0;
        }
    }

    private function transformRowToString($row) {
        $pdo = DB::connection()->getPdo();

        return '('
            . $pdo->quote($row['email_id']) . ','
            . $pdo->quote($row['feed_id']) . ','
            . $pdo->quote($row['subscribe_datetime']) . ','
            . 'NULL,'
            . $pdo->quote($row['status']) . ','
            . $pdo->quote($row['first_name']) . ','
            . $pdo->quote($row['last_name']) . ','
            . $pdo->quote($row['address']) . ','
            . $pdo->quote($row['address2']) . ','
            . $pdo->quote($row['city']) . ','
            . $pdo->quote($row['state']) . ','
            . $pdo->quote($row['zip']) . ','
            . $pdo->quote($row['country']) . ','
            . $pdo->quote($row['dob']) . ','
            . $pdo->quote($row['gender']) . ','
            . $pdo->quote($row['phone']) . ','
            . $pdo->quote($row['mobile_phone']) . ','
            . $pdo->quote($row['work_phone']) . ','
            . $pdo->quote($row['capture_date']) . ','
            . $pdo->quote($row['source_url']) . ','
            . $pdo->quote($row['ip'])
            . ')';
    }

    public function insert($row) {
        DB::statement(
            "INSERT INTO email_feed_instances
            (email_id, feed_id, subscribe_datetime, unsubscribe_datetime,
            status, first_name, last_name, address, address2, city, state, 
            zip, country, dob, gender, phone, mobile_phone, work_phone, 
            capture_date, source_url, ip )

            VALUES

            (:email_id, :feed_id, :subscribe_datetime, :unsubscribe_datetime,
            :status, :first_name, :last_name, :address, :address2, :city, :state, 
            :zip, :country, :dob, :gender, :phone, :mobile_phone, :work_phone, 
            :capture_date, :source_url, :ip )

            ON DUPLICATE KEY UPDATE
            email_id= email_id,
            feed_id= feed_id,
            subscribe_datetime= subscribe_datetime,
            unsubscribe_datetime= unsubscribe_datetime,
            status= status,
            first_name= first_name,
            last_name= last_name,
            address= address,
            address2= address2,
            city= city,
            state= state,
            zip= zip,
            country= country,
            dob= dob,
            gender= gender,
            phone= phone,
            mobile_phone= mobile_phone,
            work_phone= work_phone,
            capture_date= capture_date,
            source_url= source_url,
            ip= ip",

            array(
                ':email_id' => $row['email_id'],
                ':feed_id' => $row['feed_id'],
                ':subscribe_datetime' => $row['subscribe_datetime'],
                ':unsubscribe_datetime' => $row['unsubscribe_datetime'],
                ':status' => $row['status'],
                ':first_name' => $row['first_name'],
                ':last_name' => $row['last_name'],
                ':address' => $row['address'],
                ':address2' => $row['address2'],
                ':city' => $row['city'],
                ':state' => $row['state'],
                ':zip' => $row['zip'],
                ':country' => $row['country'],
                ':dob' => $row['dob'],
                ':gender' => $row['gender'],
                ':phone' => $row['phone'],
                ':mobile_phone' => $row['mobile_phone'],
                ':work_phone' => $row['work_phone'],
                ':capture_date' => $row['capture_date'],
                ':source_url' => $row['source_url'],
                ':ip' => $row['ip']
            )
        );        
    }

    public function getEmailInstancesAfterDate($emailId, $date, $feedId) {
        $attrDb = config('database.connections.attribution.database');

        $reps = DB::table('email_feed_instances as efi')
                ->select('efi.feed_id', 'level', 'efi.capture_date')
                ->join($attrDb . '.attribution_levels as al', 'efi.feed_id', '=', 'al.feed_id')
                ->join('feeds as f', 'efi.feed_id', '=', 'f.id')
                ->where('efi.capture_date', '>=', $date)
                ->where('efi.feed_id', '<>', $feedId)
                ->where('email_id', $emailId)
                ->where('f.party', 3)
                ->where('f.status', 'Active')
                ->orderBy('capture_date', 'asc')
                ->get();

        return $reps;
    }

    public function getInstances($emailId) {
        $attrDb = config('database.connections.attribution.database');

        $reps = DB::table('email_feed_instances as efi')
                ->select('efi.feed_id', 'level', 'efi.capture_date')
                ->join($attrDb . '.attribution_levels as al', 'efi.feed_id', '=', 'al.feed_id')
                ->join('feeds as f', 'efi.feed_id', '=', 'f.id')
                ->where('email_id', $emailId)
                ->where('f.party', 3)
                ->where('f.status', 'Active')
                ->orderBy('capture_date', 'asc')
                ->get();

        return $reps;
    }

    public function getMt1UniqueCountForFeedAndDate( $feedId , $date ) {
        $results =  DB::connection( 'mt1_data' )->table( 'ClientRecordTotalsByIsp' )
            ->select( DB::raw( "sum( uniqueRecords ) as 'uniques'" ) )
            ->where( [
                [ 'clientID' , $feedId ] ,
                [ 'processedDate' , $date ]
            ] )->get();

        if ( count( $results ) > 0 ) {
            return $results[ 0 ]->uniques;
        } else {
            return 0;
        }
    }

    public function getMt2UniqueCountForFeedAndDate( $feedId , $date ) {
        $mt2Db = config( 'database.connections.slave_data.database' );
        $attrDb = config( 'database.connections.attribution.database' );
        $reportDb = config( 'database.connections.reporting_data.database' );

        $results = DB::select( DB::raw( "
            SELECT
                COUNT( x.email_address ) AS 'uniques'
            FROM
               ( SELECT
                   e.email_address
               FROM
                   $mt2Db.email_feed_instances efi1
                   LEFT JOIN $mt2Db.email_feed_instances efi2 ON efi1.email_id = efi2.email_id
                   INNER JOIN $mt2Db.emails e ON efi1.email_id = e.id
                  
               WHERE
                   efi1.capture_date = '{$date}'
                   AND
                   efi1.id <> efi2.id
                   AND
                   efi2.capture_date <= '{$date}'
                   AND
                   efi2.id IS NULL
                   AND
                   efi1.feed_id = '{$feedId}'
             
               UNION DISTINCT
             
               SELECT
                   e.email_address
               FROM
                   $mt2Db.email_feed_instances efi
                   INNER JOIN $attrDb.email_feed_assignments efa ON efi.email_id = efa.email_id
                   INNER JOIN $mt2Db.emails e ON efa.email_id = e.id
               WHERE
                   efi.feed_id = '{$feedId}'
                   AND
                   efi.capture_date = '{$date}'
                   AND
                   efa.capture_date < '{$date}' - INTERVAL 90 DAY
                   AND
                   efi.feed_id <> efa.feed_id
                   AND
                   efi.status = 'A'
             
               UNION DISTINCT
             
               SELECT
                   e.email_address
               FROM
                   $mt2Db.email_feed_instances efi FORCE INDEX(email_client_instances_capture_date_index)
                   INNER JOIN $attrDb.email_feed_assignments efa ON efi.email_id = efa.email_id
                   LEFT JOIN $reportDb.email_campaign_statistics ecs ON efi.email_id = ecs.email_id
                   INNER JOIN $mt2Db.emails e ON efa.email_id = e.id
                   INNER JOIN $attrDb.attribution_levels alImport ON efi.feed_id = alImport.feed_id
                   INNER JOIN $attrDb.attribution_levels alOld ON efa.feed_id = alOld.feed_id
               WHERE
                   efi.capture_date = '{$date}'
                   AND
                   efa.capture_date BETWEEN '{$date}' - INTERVAL 90 DAY AND '{$date}' - INTERVAL 10 DAY
                   AND
                   alImport.level < alOld.level
                   AND
                   efi.status = 'A'
                   AND
                   efi.feed_id = '{$feedId}'
               GROUP BY
                   efi.email_id
              
               HAVING
                   SUM(IFNULL(ecs.esp_total_opens, 0)) = 0 ) x" )
        );

        if ( count( $results ) > 0 ) {
            return $results[ 0 ]->uniques;
        } else {
            return 0;
        }
    }

    public function getRecordsFromFeedStartingAt($feedId, $startingId) {
        return $this->emailFeedModel
                    ->where('feed_id', $feedId)
                    ->where('id', '>', $startingId)
                    ->orderBy('id');
    }

}
