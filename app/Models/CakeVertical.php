<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CakeVertical extends Model {
    public $timestamps = false;
    protected $guarded = [];

    public function cakeOffers() {
        return $this->hasMany('App\Models\CakeOffer');
    }
}
