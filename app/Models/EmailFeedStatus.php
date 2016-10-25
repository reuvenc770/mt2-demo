<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailFeedStatus extends Model {
  
    protected $guarded = [];
    public $timestamps = false;
    protected $table = 'email_feed_statuses';

    public function email() {
        return $this->belongsTo('App\Models\Email');
    }

    public function feed() {
        return $this->belongsTo('App\Models\Feed');
    }
}