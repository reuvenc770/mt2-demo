<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailFeedInstance extends Model {

    protected $guarded = ['id'];
    
    public function email() {
        return $this->belongsTo('App\Models\Email');
    }

    public function feed() {
        return $this->belongsTo('App\Models\Feed');
    }
}
