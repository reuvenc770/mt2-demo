<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailCampaignStatistic extends Model {
  protected $guarded = ['id'];
  protected $connection = "reporting_data";

  public function email() {
    return $this->belongsTo('App\Model\Email');
  }
}
