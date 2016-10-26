<?php

namespace App\Repositories;

use App\Models\Country;

class CountryRepo {

    private $country;

    public function __construct(Country $country) {
        $this->country = $country;
    }

    public function get() {
        return $this->country->get();
    }

}