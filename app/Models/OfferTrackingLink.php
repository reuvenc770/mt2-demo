<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferTrackingLink extends Model {
    protected $guarded = ['id'];

    public function offer() {
        return $this->belongsTo('App\Models\Offer');
    }

    public function link() { //?
        return $this->belongsTo('App\Models\Link');
    }
}
