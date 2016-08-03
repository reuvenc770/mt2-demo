<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model {
  
    protected $fillable = ['id', 'name', 'advertiser_id', 'offer_payout_type_id'];

    public function advertiser() {
        return $this->belongsTo('App\Models\Advertiser');
    }
}