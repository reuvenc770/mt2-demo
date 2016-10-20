<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $guarded = [];

    public function feeds() {
        return $this->hasMany('App\Models\Feeds');
    }
}
