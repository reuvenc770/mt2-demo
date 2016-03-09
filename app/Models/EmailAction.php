<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailAction extends Model {
  
  protected $guarded = ['id'];
  protected $connection = "reporting_data";

  public function email() {
    return $this->belongsTo('App\Model\Email');
  }

  public function actionType() {
    return $this->belongsTo('App\Model\ActionType');
  }
}
