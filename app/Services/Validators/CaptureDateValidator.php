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
        return ['captureDate'];
    }

    public function setData(array $data) {
        $this->captureDate = $data['captureDate'];
    }

    public function validate() {
        $today = Carbon::today();

        try {
            $parsedDate = Carbon::parse($this->captureDate);

            // The system doesn't handle capture dates in the future too well.
            if ($parsedDate->gt($today) || '0000-00-00' === $this->captureDate) {
                $this->captureDate = $today->format('Y-m-d');
            }
        }
        catch (Exception $e) {
            // Carbon throws this is it can't parse the date.
            // In that case, throw validation error, as per Ken's request

            // According to Jim, the date formats currently received look like
            // yyyy-mm-dd hh:mm:ss or mm/dd/yyyy hh:mm:ss, but we should not rule out other types 
            // and should check the errors logs often to find new parsable formats.
            throw new ValidationException("Capture date '{$this->captureDate}' cannot be parsed");
        }
        
    }

    public function returnData() {
        return ['captureDate' => $this->captureDate];
    }

}