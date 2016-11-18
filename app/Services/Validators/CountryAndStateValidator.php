<?php

namespace App\Services\Validators;

use App\Services\Interfaces\IValidate;
use App\Exceptions\ValidationException;

class CountryAndStateValidator implements IValidate {

    private $state;
    private $country;

    const US_ALIASES = ['UNITED STATES', 'US', 'USA'];
    const CA_ALIASES = ['CANADA', 'CA'];

    const US_STATES = ['AE', 'AK', 'AL', 'AP', 'AR', 'AS', 'AZ', 'CA', 'CO', 'CT', 'DC', 'DE', 'FL', 
    'GA', 'GU', 'HI', 'IA', 'ID', 'IL', 'IN', 'KS', 'KY', 'LA', 'MA', 'MD', 'ME', 'MI', 'MN', 'MO', 
    'MP', 'MS', 'MT', 'NC', 'ND', 'NE', 'NH', 'NJ', 'NM', 'NV', 'NY', 'OH', 'OK', 'OR', 'PA', 'PR', 
    'RI', 'SC', 'SD', 'TN', 'TX', 'UT', 'VA', 'VI', 'VT', 'WA', 'WI', 'WV', 'WY'];

    const CA_PROVINCES = ['AB', 'BC', 'MB', 'NB', 'NF', 'NS', 'NT', 'ON', 'PE', 'QC', 'SK', 'YT'];

    public function __construct() {}

    public function getRequiredData() {
        return ['state', 'country'];
    }

    public function setData(array $data) {
        $this->state = $data['state'];
        $this->country = $data['country'];
    }


    public function validate() {
        $this->country = strtoupper($this->country);
        $this->state = strtoupper($this->state);

        if (in_array($this->country, self::US_ALIASES)) {
            $this->country = 'US';

            if (!in_array($this->state, self::US_STATES)) {
                $this->state = '';
            }
        }

        elseif (in_array($this->country, self::CA_ALIASES)) {
            throw new ValidationException("Canada detected for country: {$this->country}");
        }
    }


    public function returnData() {
        return [
            'state' => $this->state,
            'country' => $this->country
        ];
    }

}