<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ClientFeed extends Model {

    public function client() {
        return $this->belongsTo('App\Models\Client');
    }

    public function records() {
        return $this->hasMany('App\Models\ClientFeedRecords');
    }
}