<?php

namespace App\Services\Validators;

use App\Services\Interfaces\IValidate;
use App\Exceptions\ValidationException;
use Carbon\Carbon;
use Exception;

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

            try {
                $underEighteen = Carbon::parse($this->dob)->gte($eighteenYearsAgo);
            }
            catch (Exception $e) {
                throw new ValidationException("Could not determine user's age: {$this->dob}.");
            }

            if ($underEighteen) {
                throw new ValidationException("User must be at least 18 years old - {$this->dob}.");
            }
        }
    }

    public function returnData() {
        return ['dob' => $this->dob];
    }

}