<?php

namespace App\Services;

use App\Repositories\IpligenceDataRepo;
use App\Repositories\Ipv6CountryMappingRepo;

class IpService {
    
    private $ipv4Repo;
    private $ipv6Repo;

    public function __construct(IpligenceDataRepo $ipv4Repo, Ipv6CountryMappingRepo $ipv6Repo) {
        $this->ipv4Repo = $ipv4Repo;
        $this->ipv6Repo = $ipv6Repo;
    }

    public function isFromCanada($ip) {
        if ($this->isIPv6($ip)) {
            return $this->ipv6Repo->isFromCanada($ip);
        }
        elseif ($this->isIPv4($ip)) {
            return $this->ipv4Repo->isFromCanada($ip);
        }
        else {
            throw new \Exception("$ip is not a valid ip address of any type");
        }
    }

    public function isIPv6($ip) {
        return filter_var($ip, FILTER_VALIDATE_IP, ['flags' => FILTER_FLAG_IPV6]) !== false;
    }

    public function isIPv4($ip) {
        return filter_var($ip, FILTER_VALIDATE_IP, ['flags' => FILTER_FLAG_IPV4]) !== false;
    }
}