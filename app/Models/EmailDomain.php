<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailDomain extends Model {
    
  public function email() {
    return $this->hasMany('App\Models\Email');
  }

  public function domainGroup() {
    return $this->belongsTo('App\Models\DomainGroup');
  }
}
