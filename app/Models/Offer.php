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
}