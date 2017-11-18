<?php

namespace App\Repositories;

use App\Models\CakeRedirectDomain;
use App\Exceptions\ValidationException;
use App\Models\OfferPayoutType;
use Mail;

class CakeRedirectDomainRepo {
    
    private $model;
    private $types;

    public function __construct(CakeRedirectDomain $model, OfferPayoutType $types) {
        $this->model = $model;
        $this->types = $types;
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
            $type = $this->types->find($offerTypeId)->name;
            $message = "$affiliateId does not have a redirect domain set for $type";
            
            Mail::raw($message, function($mail) {
                $mail->subject("Affiliate without redirect domain");
                $message->to(config('contacts.ops'));
            });

            throw new ValidationException($message);
            #return config('misc.esp_cake_redir_domain'); // default value
        }
    }

    public function getDefaultRedirectDomain() {
        return config('misc.cake_old_redir_domain');
    }
}