<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListProfileOfferAction extends Model
{
    protected $guarded = [];
    public $timestamps = false;
    protected $connection = 'list_profile';
    protected $table = 'list_profile_offer_actions';

    public function offer() {
        return $this->belongsTo('App\Models\Offer');
    }

    public function listProfile() {
        return $this->belongsTo('App\Models\ListProfile');
    }
}