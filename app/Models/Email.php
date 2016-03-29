<?php

namespace App\Models;

use App\Models\Email;
use Illuminate\Database\Eloquent\Model;

class Email extends Model {

  protected $guarded = ['id'];
  public $timestamps = false;
    
  public function emailClientInstances() {
    return $this->hasMany('App\Models\EmailClientInstance');
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

}
