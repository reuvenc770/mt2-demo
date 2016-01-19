<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EspAccount extends Model
{
    public function esp()
    {
        return $this->belongsTo('App\Models\Esp');
    }

    public function getFirstKey()
    {
        return $this->attributes['key_1'];
    }

    public function getSecondKey()
    {
        return $this->attributes['key_2'];
    }

}
