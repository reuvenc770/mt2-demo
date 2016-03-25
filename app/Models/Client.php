<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model {
    
    public function emailClientInstances() {
        return $this->hasMany('App\Models\EmailClientInstance');
    }

    public function emailAction() {
        return $this->hasMany('App\Models\EmailAction');
    }
}
