<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailCampaignStatistic extends Model {
  protected $guarded = ['id'];
  protected $connection = "reporting_data";
  public $timestamps = false;

  public function email() {
    return $this->belongsTo('App\Models\Email');
  }

  public function deploy() {
    return $this->belongsTo('App\Models\Deploy');
  }

  public function userAgent() {
    return $this->belongsTo('App\Models\UserAgentString');
  }
}
