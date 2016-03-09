<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActionType extends Model {
  
  protected $guarded = ['id'];
  protected $connection = "reporting_data";

  public function emailActions() {
    return $this->hasMany('App\Model\EmailAction');
  }
}
