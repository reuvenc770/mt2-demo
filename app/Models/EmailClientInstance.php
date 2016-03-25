<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailClientInstance extends Model {
    
    public function email() {
        return $this->belongsTo("App\Models\Email");
    }

    public function client() {
        return $this->belongsTo('App\Models\Client');
    }
}
