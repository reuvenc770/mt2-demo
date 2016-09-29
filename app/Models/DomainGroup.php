<?php

namespace App\Models;

use App\Models\ModelTraits\ModelCacheControl;
use Illuminate\Database\Eloquent\Model;

class DomainGroup extends Model {

  use ModelCacheControl;

  public function domains() {
    return $this->hasMany("App\\Models\\EmailDomain");
  }

  public function emails() {
    return $this->hasManyThrough('App\Model\Email', 'App\Model\EmailDomain');
  }
}
