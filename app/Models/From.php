<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class From extends Model
{
    protected $guarded = [];

    public function deploys() {
        return $this->hasMany('App\Models\Deploys');
    }

    public function openRate() {
        return $this->hasOne('App\Models\FromOpenRate');
    }
}