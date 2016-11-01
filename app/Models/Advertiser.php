<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Advertiser extends Model {
  
    protected $fillable = ['id', 'name'];

    public function offers() {
        return $this->hasMany('App\Models\Offer');
    }
}