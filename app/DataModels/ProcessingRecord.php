<?php

namespace App\DataModels;

class ProcessingRecord {

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

    public function __construct(array $data) {

    }

    public function __get($prop) {
        return isset($this->$prop) ? $this->$prop : '';
    }

    public function __set($prop, $value) {
        if (isset($this->$prop)) {
            $this->$prop = $value;
        }
    }

    public function validateConsistency() {}


}