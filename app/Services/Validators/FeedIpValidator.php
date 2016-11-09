<?php

namespace App\Services\Validators;

use App\Services\Interfaces\IValidate;
use App\Exceptions\ValidationException;
use App\Repositories\IpligenceDataRepo;

class FeedIpValidator implements IValidate {

    private $feedIp;
    private $ipRepo;

    public function __construct(IpligenceDataRepo $ipRepo) {
        $this->ipRepo = $ipRepo;
    }

    public function getRequiredData() {
        return ['feed_ip'];
    }

    public function setData(array $data) {
        $this->feedIp = $data['feed_ip'];
    }

    public function validate() {
        if (!filter_var($this->feedIp, FILTER_VALIDATE_IP)) {
            throw new ValidationException("Invalid IP format detected {$this->feedIp}");
        }

        if ($this->ipRepo->isFromCanada($this->feedIp)) {
            throw new ValidationException("Canadian feed IP detected {$this->feedIp}");
        }
    }

    public function returnData() {
        return ['feed_ip' => $this->feedIp];
    }

}