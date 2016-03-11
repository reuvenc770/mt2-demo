<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Email extends Model {
    
  public function emailClientInstances() {
    return $this->hasMany('App\Model\EmailClientInstance');
  }

  public function emailDomain() {
    return $this->belongsTo('App\Model\EmailDomain');
  }

  public function emailAction() {
    return $this->hasMany('App\Model\EmailAction');
  }

  public function emailCampaignStatistic() {
    return $this->hasMany('App\Model\emailCampaignStatistic');
  }

}
