<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeployRecordRepull extends Model {
  
  public function report() {
    return $this->belongsTo('App\Model\StandardReport');
  }
}