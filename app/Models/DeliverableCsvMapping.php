<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliverableCsvMapping extends Model {

    protected $guarded = ['id'];

    public function esp() {
        return $this->belongsTo('App\Model\Esp');
    }
}