<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ModelTraits\Mailable;

class Offer extends Model {

    use Mailable;
  
    protected $guarded = [];

    public function advertiser() {
        return $this->belongsTo('App\Models\Advertiser');
    }

    public function trackingLinks() {
        return $this->hasMany('App\Models\OfferTrackingLink');
    }

    public function payoutType() {
        return $this->belongsTo('App\Models\OfferPayoutType', 'offer_payout_type_id');
    }

    public function cakeOffers() {
        return $this->belongsToMany('App\Models\CakeOffer', 'mt_offer_cake_offer_mappings', 'offer_id', 'cake_offer_id');
    }
}