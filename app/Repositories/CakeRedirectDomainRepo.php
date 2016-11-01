<?php

namespace App\Repositories;

use App\Models\CakeRedirectDomain;

class CakeRedirectDomainRepo {
    
    private $model;

    public function __construct(CakeRedirectDomain $model) {
        $this->model = $model;
    }

    public function getRedirectDomain($affiliateId, $offerTypeId) {
        $result = $this->model
                      ->select('redirect_domain')
                       ->where('cake_affiliate_id', $affiliateId)
                       ->where('offer_payout_type_id', $offerTypeId)
                       ->first();

        if ($result) {
            return $result->redirect_domain;
        }
        else {
            return env('ESP_CAKE_REDIR_DOMAIN', ''); // default value
        }
    }

    public function getDefaultRedirectDomain() {
        return env('DEFAULT_CAKE_REDIRECT_DOMAIN');
    }
}