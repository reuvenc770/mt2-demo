<?php

namespace App\Services;

use App\Repositories\ClientPayoutRepo;

class ClientPayoutService {
    
    private $payoutRepo;

    public function __construct(ClientPayoutRepo $payoutRepo) {
        $this->payoutRepo = $payoutRepo;
    }

    public function getTypes() {
        return $this->payoutRepo->getTypes();
    }

    public function setPayout($clientId, $payoutType, $payoutAmount) {
        $this->payoutRepo
             ->setPayout($clientId, $payoutType, $payoutAmount);
    }

    public function getPayout($clientId) {
        return $this->payoutRepo->getPayout($clientId);
    }
}