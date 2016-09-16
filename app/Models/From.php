<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ModelTraits\Mailable;

class From extends Model
{
    use Mailable;

    protected $guarded = [];

    public function deploys() {
        return $this->hasMany('App\Models\Deploys');
    }

    public function openRate() {
        return $this->hasOne('App\Models\FromOpenRate');
    }
}