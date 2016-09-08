<?php

namespace App\Services;

use App\Repositories\OfferPayoutRepo;

class OfferPayoutService {
    
    private $payoutRepo;

    public function __construct(OfferPayoutRepo $payoutRepo) {
        $this->payoutRepo = $payoutRepo;
    }

    public function getTypes() {
        return $this->payoutRepo->getTypes();
    }

    public function setPayout($offerId, $payoutType, $payoutAmount) {
        $this->payoutRepo
             ->setPayout($offerId, $payoutType, $payoutAmount);
    }

    public function getPayout($offerId) {
        return $this->payoutRepo->getPayout($offerId);
    }
}