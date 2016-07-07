<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientFeedRecord extends Model {
    protected $fillable = ['id', 'email_address', 'client_feed_id', 'capture_date', 
    'source_url', 'ip', 'first_name', 'last_name', 'address', 'address2', 'city', 
    'state', 'zip', 'birth_date', 'gender', 'phone', 'valid'];

    public function feed() {
        return $this->belongsTo('App\Models\ClientFeed');
    }
}
