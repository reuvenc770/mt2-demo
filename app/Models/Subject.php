<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $guarded = [];

    public function deploys() {
        return $this->hasMany('App\Models\Deploys');
    }

    public function openRate() {
        return $this->hasOne('App\Models\SubjectOpenRate');
    }
}
