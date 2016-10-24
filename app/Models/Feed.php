<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Feed extends Model {

    protected $guarded = [];

    public function emailFeedInstances() {
        return $this->hasMany('App\Models\EmailFeedInstance');
    }

    public function attributionLevel() {
        return $this->hasOne('App\Models\AttributionLevel');
    }

    public function client() {
        return $this->belongsTo('App\Models\Client');
    }
}
