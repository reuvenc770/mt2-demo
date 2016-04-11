<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailCampaignStatistic extends Model {
  protected $guarded = ['id'];
  protected $connection = "reporting_data";

  public function email() {
    return $this->belongsTo('App\Models\Email');
  }

  public function campaign() {
    // Need to create some notion of a deploys/campaign table
    // return $this->belongsTo('App\Model\Campaign');
    // current stand-in
    return 1;
  }

  public function userAgent() {
    return $this->belongsTo('App\Models\UserAgentString');
  }
}
