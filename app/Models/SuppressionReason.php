<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuppressionReason extends Model {

    protected $guarded = ['id'];
    public $timestamps = false;

    public function suppressions() {
        return $this->hasMany('App\Model\Suppression');
    }

    public function esp()
    {
        return $this->belongsTo('App\Models\Esp');
    }
}
