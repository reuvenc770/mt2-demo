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

    public function __construct(EmailFeedInstance $emailFeedModel) {
        $this->emailFeedModel = $emailFeedModel;
    }

    public function getEmailId($emailAddress) {
        #return $this->emailModel->select( 'id' )->where( 'email_address' , $email )->get();
        return mt_rand(1, 100000);
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
                #->join(FEEDS_TABLE, 'efi.feed_id', '=', 'cf.id') -- see above: placeholder for feeds
                ->where('efi.capture_date', '>=', $date)
                ->where('efi.feed_id', '<>', $feedId)
                ->where('email_id', $emailId)
                #->where('cf.level', 3)
                ->orderBy('capture_date', 'asc')
                ->get();

        return $reps;
    }

    public function getInstances($emailId) {
        $attrDb = config('database.connections.attribution.database');

        $reps = DB::table('email_feed_instances as efi')
                ->select('efi.feed_id', 'level', 'efi.capture_date')
                ->join($attrDb . '.attribution_levels as al', 'efi.feed_id', '=', 'al.feed_id')
                #->join(FEEDS_TABLE, 'efi.feed_id', '=', 'cf.id') -- see above: placeholder for feeds
                ->where('email_id', $emailId)
                #->where('cf.level', 3)
                ->orderBy('capture_date', 'asc')
                ->get();

        return $reps;
    }

    public function getMt1UniqueCountForFeedAndDate( $feedId , $date ) {
        $results =  DB::connection( 'mt1mail' )->table( 'ClientRecordTotalsByIsp' )
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
        $mt2Db = config('database.connections.slave_data.database');

        $results = DB::select( DB::raw( "
            SELECT
                COUNT( * ) AS 'uniques'
            FROM
                {$mt2Db}.email_feed_instances e1
                LEFT JOIN {$mt2Db}.email_feed_instances e2 ON( e1.email_id = e2.email_id AND e1.id <> e2.id AND e2.capture_date < :dateCeiling )
            WHERE
                e1.feed_id = :feedId
                AND e1.capture_date = :date
                AND e2.id IS NULL" ) , 
            [ ':dateCeiling' => $date , ':date' => $date , ':feedId' => $feedId ]     
        );

        if ( count( $results ) > 0 ) {
            return $results[ 0 ]->uniques;
        } else {
            return 0;
        }
    }

}
