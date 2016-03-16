<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailDomain extends Model {
    
  public function email() {
    return $this->hasMany('App\Model\Email');
  }

  public function domainGroup() {
    return $this->belongsTo('App\Model\DomainGroup');
  }
}
