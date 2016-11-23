<?php

namespace App\Services\Validators;

use App\Services\Interfaces\IValidate;
use App\Exceptions\ValidationException;
use Carbon\Carbon;

class AgeValidator implements IValidate {

    private $dob;

    public function __construct() {}

    public function getRequiredData() {
        return ['dob'];
    }

    public function setData(array $data) {
        $this->dob = $data['dob'];
    }

    public function validate() {
        
        if ($this->dob) {
            $eighteenYearsAgo = Carbon::today()->subYears(18);

            if (Carbon::parse($this->dob)->gt($eighteenYearsAgo)) {
                throw new ValidationException("User must be at least 18 years old - {$this->dob}");
            }
        }
    }

    public function returnData() {
        return ['dob' => $this->dob];
    }

}