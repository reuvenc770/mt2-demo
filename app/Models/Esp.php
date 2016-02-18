<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Esp extends Model
{

    public function espAccounts()
    {
        return $this->hasMany('App\Models\EspAccount');
    }
}
