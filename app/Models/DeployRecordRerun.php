<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeployRecordRerun extends Model {

    protected $fillable = ['deploy_id', 'esp_internal_id', 'esp_account_id', 'delivers', 'opens', 'clicks'];
    public $timestamps = false;
    protected $primaryKey = 'deploy_id';
  
    public function report() {
        return $this->belongsTo('App\Models\StandardReport');
    }

    public function espAccount() {
        return $this->belongsTo('App\Models\EspAccount');
    }
}