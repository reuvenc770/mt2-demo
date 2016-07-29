<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Advertiser extends Model {
  
  protected $guarded = ['id'];

  public function offers() {
    return $this->hasMany('App\Models\Offer');
  }
}