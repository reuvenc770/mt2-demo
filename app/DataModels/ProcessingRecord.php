<?php

namespace App\DataModels;

use Carbon\Carbon;

class ProcessingRecord {

    const FIELDS = ['emailId', 'feedId', 'emailAddress', 'isSuppressed', 'firstName', 'lastName', 
    'address', 'address2', 'city', 'state', 'zip', 'country', 'dob', 'gender', 'phone', 'captureDate',
    'ip', 'sourceUrl', 'otherFields', 'isDeliverable', 'uniqueStatus', 'newEmail', 'domainId', 'isValid',
    'invalidReason'];

    private $emailId;
    private $feedId;
    private $emailAddress;
    private $isSuppressed = false;
    private $firstName;
    private $lastName;
    private $address;
    private $address2;
    private $city;
    private $state;
    private $zip;
    private $country;
    private $dob;
    private $gender;
    private $phone;
    private $captureDate;
    private $ip;
    private $sourceUrl;    
    private $otherFields = [];
    private $otherFieldsJson = '';
    private $isDeliverable;

    // Metadata
    private $uniqueStatus = 'unique'; // unique, duplicate, non-unique
    private $newEmail = null;
    private $domainId;
    private $processDate;
    private $isValid;
    private $invalidReason;

    public function __construct(array $data) {
        $this->processDate = Carbon::today()->format('Y-m-d');
/**
    Need to determine $newEmail, $domainId after instantiation

*/
    }

    public function __get($prop) {
        return isset($this->$prop) ? $this->$prop : '';
    }

    public function __set($prop, $value) {
        if (in_array($prop, self::FIELDS)) {
            $this->$prop = $value;
        }
    }

    public function validateConsistency() {}

    public function mapToEmails() {
        return [
            'id' => $this->emailId,
            'email_address' => $this->emailAddress,
            'email_domain_id' => $this->domainId,
        ];
    }

    public function mapToInstances() {
        return [
            'email_id' => $this->emailId,
            'feed_id' => $this->feedId,
            'subscribe_datetime' => $this->processDate,
            //'unsubscribe_datetime' => $this->,
            'status' => $this->isSuppressed ? 'U' : 'A',
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'address' => $this->address,
            'address2' => $this->address2,
            'city' => $this->city,
            'state' => $this->state,
            'zip' => $this->zip,
            'country' => $this->country,
            'dob' => $this->dob,
            'gender' => $this->gender,
            'phone' => $this->phone,
            'mobile_phone' => '',
            'work_phone' => '',
            'capture_date' => $this->captureDate,
            'source_url' => $this->sourceUrl,
            'ip' => $this->ip
        ];
    }

    public function mapToRecordData() {
        return [
            'email_id' => $this->emailId,
            'feed_id' => $this->feedId,
            'subscribe_date' => $this->processDate, // Pehaps we should drop this
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'address' => $this->address,
            'address2' => $this->address2,
            'city' => $this->city,
            'state' => $this->state,
            'zip' => $this->zip,
            'country' => $this->country,
            'dob' => $this->dob,
            'gender' => $this->gender,
            'phone' => $this->phone,
            'capture_date' => $this->captureDate,
            'source_url' => $this->sourceUrl,
            'ip' => $this->ip,
            'is_deliverable' => $this->isDeliverable, 
            'other_fields' => $this->otherFieldsJson
        ];
    }


}
