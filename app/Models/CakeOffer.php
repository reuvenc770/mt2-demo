<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CakeOffer extends Model {
    public $timestamps = false;
    protected $guarded = [];

    public function vertical() {
        return $this->belongsTo('App\Models\CakeVertical');
    }

    public function mtOffers() {
        return $this->belongsToMany('App\Models\CakeOffer', 'mt_offer_cake_offer_mappings', 'cake_offer_id', 'offer_id');
    }
}
