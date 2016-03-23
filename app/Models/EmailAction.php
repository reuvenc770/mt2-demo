<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailAction extends Model {
  
  protected $guarded = ['id'];
  protected $connection = "reporting_data";

  public function email() {
    return $this->belongsTo('App\Models\Email');
  }

  public function actionType() {
    return $this->belongsTo('App\Models\ActionType');
  }
}
