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
        if ($this->ipRepo->isFromCanada($this->feedId)) {
            throw new ValidationException("Canadian Feed ip detected");
        }
    }

    public function returnData() {
        return ['feed_ip' => $this->feedIp];
    }

}