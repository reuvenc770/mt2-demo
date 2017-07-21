<?php

namespace App\DataModels;

use App\Models\RawFeedEmail;
use Carbon\Carbon;

class ProcessingRecord {

    const FIELDS = ['emailId', 'feedId', 'emailAddress', 'isSuppressed', 'firstName', 'lastName', 
    'address', 'address2', 'city', 'state', 'zip', 'country', 'dob', 'gender', 'phone', 'captureDate',
    'ip', 'sourceUrl', 'otherFields', 'uniqueStatus', 'newEmail', 'domainId', 'valid',
    'invalidReason', 'domainGroupId', 'attrStatus', 'processDateTime', 'processDate', 'otherFieldsJson', 'otherFields'];

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
    private $dob = null;
    private $gender;
    private $phone;
    private $captureDate = null;
    private $ip;
    private $sourceUrl;    
    private $otherFields = [];
    private $otherFieldsJson = '';
    private $attrStatus;
    private $processDateTime;
    private $file = '';

    // Metadata
    private $uniqueStatus = null; // unique, duplicate, non-unique
    private $newEmail;
    private $domainId;
    private $domainGroupId;
    private $processDate;
    private $isValid;
    private $invalidReason;

    public function __construct(RawFeedEmail $record) {
        $this->processDate = Carbon::today()->toDateString();
        $this->processDateTime = $record->created_at;

        $this->emailAddress = $record->email_address;

        // email id, new email status, domain id, and domain group id to be set later
        if (null !== $record->email_id) {
            $this->newEmail = false;
            $this->emailId = $record->email_id;
            $this->domainId = $record->email_domain_id;
            $this->domainGroupId = $record->domain_group_id;
            
        }
        else {
            $this->newEmail = true;
            $this->emailId = null;
            $this->domainId = null;
            $this->domainGroupId = null;
        }

        $this->attrStatus = null;
        $this->isSuppressed = ((int)$record->suppressed === 1);
        
        // The rest we already know
        $this->feedId = $record->feed_id;
        $this->firstName = $record->first_name;
        $this->lastName = $record->last_name;
        $this->address = $record->address;
        $this->address2 = $record->address2;
        $this->city = $record->city;
        $this->state = $record->state;
        $this->zip = $record->zip;
        $this->country = $record->country;
        $this->dob = $record->dob;
        $this->gender = $record->gender;
        $this->phone = $record->phone;
        $this->captureDate = $record->capture_date; // Validation / correction of this value is performed in the CaptureDateValidator
        $this->ip = $record->ip;
        $this->sourceUrl = $record->source_url;
        $this->otherFieldsJson = $record->other_fields ?: '{}';
        $this->otherFields = json_decode($record->other_fields, true);
        $this->file = $record->realtime === 0 ? $this->stripFile($record->file) : 'Realtime';
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

    private function stripFile($filePath) {
        // given a filepath like
        // /var/local/programdata/done/mt2_realtime/realtime_dev.aspiremail.mtroute.net_201777174.dat (for realtime)
        // /home/orangegenesis/Zeta Interactive 2017_07_07_085004.csv (for batch)
        $paths = explode('/', $filePath);
        $index = sizeof($paths) - 1;

        if ($index >= 0) {
            return $paths[$index];
        }
        else {
            return '';
        }
    }

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
            'subscribe_date' => $this->processDate,
            'subscribe_datetime' => $this->processDateTime,
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
            'ip' => $this->ip,
            'other_fields' => $this->otherFieldsJson
        ];
    }

    public function mapToRecordData() {
        return [
            'email_id' => $this->emailId,
            'feed_id' => $this->feedId,
            'subscribe_date' => $this->processDate,
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
            'attribution_status' => $this->attrStatus, 
            'other_fields' => $this->otherFieldsJson
        ];
    }

    public function mapToEmailFeedAction($status) {
        return [
            'email_id' => $this->emailId,
            'feed_id' => $this->feedId,
            'action_type' => $status,
            'offer_id' => null,
            'esp_account_id' => null,
            'datetime' => null
        ];
    }

    public function mapToNewRecords() {
        return [
            'email_id' => $this->emailId,
            'feed_id' => $this->feedId,
            'subscribe_date' => $this->processDate
        ];
    }

}
