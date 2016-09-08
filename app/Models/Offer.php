<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model {
  
    protected $guarded = [];

    public function advertiser() {
        return $this->belongsTo('App\Models\Advertiser');
    }

    public function trackingLinks() {
        return $this->hasMany('App\Models\OfferTrackingLink');
    }
}