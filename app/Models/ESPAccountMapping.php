<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EspAccountMapping extends Model
{
    public function espAccount()
    {
        return $this->belongsTo('App\Models\EspAccount');
    }
}
