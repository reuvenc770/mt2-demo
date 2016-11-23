<?php

namespace App\Services\Validators;

use App\Services\Interfaces\IValidate;

class PhoneValidator implements IValidate {

    private $phone;

    public function __construct() {}

    public function getRequiredData() {
        return ['phone'];
    }

    public function setData(array $data) {
        $this->phone = $data['phone'];
    }

    public function validate() {
        // Strip out all non-numbers
        $this->phone = preg_replace('/[^0-9]+/', '', $this->phone);

        // We don't actually do any phone validation here yet
    }

    public function returnData() {
        return ['phone' => $this->phone];
    }

}