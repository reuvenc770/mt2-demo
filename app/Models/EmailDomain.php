<?php

namespace App\Models;

use App\Models\ModelTraits\ModelCacheControl;
use Illuminate\Database\Eloquent\Model;

class EmailDomain extends Model {

  use ModelCacheControl;
  protected $guarded = ['id'];
  public $timestamps = false;
  public function email() {
    return $this->hasMany('App\Models\Email');
  }

  public function domainGroup() {
    return $this->belongsTo('App\Models\DomainGroup');
  }
}
