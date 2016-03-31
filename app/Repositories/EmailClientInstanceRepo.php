<?php

namespace App\Repositories;

use App\Models\EmailClientInstance;
use DB;
use Illuminate\Database\Query\Builder;

/**
 *
 */
class EmailClientInstanceRepo {

    private $emailClientModel;

    public function __construct(EmailClientInstance $emailClientModel) {
        $this->emailClientModel = $emailClientModel;
    }

    public function getEmailId($emailAddress) {
        #return $this->emailModel->select( 'id' )->where( 'email_address' , $email )->get();
        return mt_rand(1, 100000);
    }

    public function insert($row) {        
        DB::statement(
            "INSERT INTO email_client_instances
            (email_id, client_id, subscribe_datetime, unsubscribe_datetime,
            status, first_name, last_name, address, address2, city, state, 
            zip, country, dob, gender, phone, mobile_phone, work_phone, 
            capture_date, source_url, ip )

            VALUES

            (:email_id, :client_id, :subscribe_datetime, :unsubscribe_datetime,
            :status, :first_name, :last_name, :address, :address2, :city, :state, 
            :zip, :country, :dob, :gender, :phone, :mobile_phone, :work_phone, 
            :capture_date, :source_url, :ip )

            ON DUPLICATE KEY UPDATE
            email_id= email_id,
            client_id= client_id,
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
                ':client_id' => $row['client_id'],
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

}