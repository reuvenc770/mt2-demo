<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Feed extends Model {

    protected $guarded = [];
    
    public function emailFeedInstances() {
        return $this->hasMany('App\Models\EmailFeedInstance');
    }

}
