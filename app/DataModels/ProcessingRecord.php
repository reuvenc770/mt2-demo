<?php

namespace App\DataModels;

use App\Models\FawFeedEmail;
use Carbon\Carbon;

class ProcessingRecord {

    const FIELDS = ['emailId', 'feedId', 'emailAddress', 'isSuppressed', 'firstName', 'lastName', 
    'address', 'address2', 'city', 'state', 'zip', 'country', 'dob', 'gender', 'phone', 'captureDate',
    'ip', 'sourceUrl', 'otherFields', 'isDeliverable', 'uniqueStatus', 'newEmail', 'domainId', 'isValid',
    'invalidReason', 'domainGroupId'];

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
    private $isDeliverable = null;

    // Metadata
    private $uniqueStatus = 'unique'; // unique, duplicate, non-unique
    private $newEmail;
    private $domainId;
    private $domainGroupId;
    private $processDate;
    private $isValid;
    private $invalidReason;

    public function __construct(RawFeedEmail $record) {
        $this->processDate = Carbon::today()->format('Y-m-d');

        $this->emailAddress = $record->emailAddress;

        if ($record->email_id) {
            $this->emailId = $record->email_id;
            $this->newEmail = false;
            $this->domainId = $record->domainId;
            $this->domainGroupId = $record->domainGroupid;
        }
        else {
            $this->newEmail = true;
            $this->emailId = null;
            $this->domainId = null;
            $this->domainGroupid = null;
        }

        $this->feedId = $record->feed_id;
        $this->firstName = $record->first_name;
        $this->lastName = $record->lastName;
        $this->address = $record->address;
        $this->address2 = $record->address2;
        $this->city = $record->city;
        $this->state = $record->state;
        $this->zip = $record->zip;
        $this->country = $record->country;
        $this->dob = $record->dob;
        $this->gender = $record->gender;
        $this->phone = $record->phone;
        $this->captureDate = $record->capture_date;
        $this->ip = $record->ip;
        $this->sourceUrl = $record->sourceUrl;
        $this->otherFieldsJson = $this->other_fields;
        $this->otherFields = json_decode($this->other_fields, true);
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
            'lower_case_md5' => md5(strtolower($this->emailAddress)),
            'upper_case_md5' => md5(strtoupper($this->emailAddress))
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