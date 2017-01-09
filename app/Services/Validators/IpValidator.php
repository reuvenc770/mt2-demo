<?php

namespace App\Services\Validators;

use App\Services\Interfaces\IValidate;
use App\Exceptions\ValidationException;
use App\Repositories\IpligenceDataRepo;

class IpValidator implements IValidate {

    private $ip;
    private $ipRepo;

    public function __construct(IpligenceDataRepo $ipRepo) {
        $this->ipRepo = $ipRepo;
    }

    public function getRequiredData() {
        return ['ip'];
    }

    public function setData(array $data) {
        $this->ip = $data['ip'];
    }

    public function validate() {
        
        if ('' === $this->ip) {
            $this->ip = '10.1.2.3';
        }
        elseif (!filter_var($this->ip, FILTER_VALIDATE_IP)) {
            throw new ValidationException("Invalid IP format detected {$this->ip}");
        }

        if ($this->ipRepo->isFromCanada($this->ip)) {
            throw new ValidationException("Canadian feed IP detected {$this->ip}");
        }
    }

    public function returnData() {
        return ['ip' => $this->ip];
    }

}