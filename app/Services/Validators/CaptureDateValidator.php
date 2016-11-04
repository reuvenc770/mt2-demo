<?php

namespace App\Services\Validators;

use App\Services\Interfaces\IValidate;
use App\Exceptions\ValidationException;
use Carbon\Carbon;
use Exception;

class CaptureDateValidator implements IValidate {

    private $captureDate;

    public function __construct() {}

    public function getRequiredData() {
        return ['capture_date'];
    }

    public function setData(array $data) {
        $this->captureDate = $data['capture_date'];
    }

    public function validate() {
        $today = Carbon::today();

        try {
            $parsedDate = Carbon::parse($this->captureDate);

            // The system doesn't handle capture dates in the future too well.
            if ($parsedDate->gt($today)) {
                $this->captureDate = $today->format('Y-m-d');
            }
        }
        catch (Exception $e) {
            // Carbon throws this is it can't parse the date.
            // In that case, set captureDate to today.
            $this->captureDate = $today->format('Y-m-d');
        }
        
    }

    public function returnData() {
        return ['capture_date' => $this->captureDate];
    }

}