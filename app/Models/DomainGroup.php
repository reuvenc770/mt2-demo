<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DomainGroup extends Model {
  
  public function domains() {
    return $this->hasMany("App\Model\EmailDomain");
  }

  public function emails() {
    return $this->hasManyThrough('App\Model\Email', 'App\Model\EmailDomain');
  }
}
