<?php

namespace App\Repositories;

use App\Models\IpligenceData;

class IpligenceDataRepo {
    private $model;

    public function __construct(IpligenceData $model) {
        $this->model = $model;
    }

    public function isFromCanada($ip) {
        $result = $this->model->whereRaw("ip_to >= inet_aton('{$ip}')")->whereRaw("ip_from <= inet_aton('{$ip}')")->first();
    
        if ($result) {
            return $result->country_code === 'CA';
        }
        else {
            return false;
        }
    }

}