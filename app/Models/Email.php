<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Email extends Model {

  protected $guarded = ['id'];
  public $timestamps = false;
    
  public function emailFeedInstances() {
    return $this->hasMany('App\Models\EmailFeedInstance');
  }

  public function emailDomain() {
    return $this->belongsTo('App\Models\EmailDomain');
  }

  public function emailAction() {
    return $this->hasMany('App\Models\EmailAction');
  }

  public function emailCampaignStatistic() {
    return $this->hasMany('App\Models\EmailCampaignStatistic');
  }

  public function suppressions() {
    return $this->hasMany('App\Models\Suppression');
  }

}
