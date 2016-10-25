<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListProfileOffer extends Model
{
    protected $guarded = [''];
    public $timestamps = false;
    protected $connection = 'list_profile';

    public function offer() {
        return $this->belongsTo('App\Models\Offer');
    }

    public function listProfile() {
        return $this->belongsTo('App\Models\ListProfile');
    }
}