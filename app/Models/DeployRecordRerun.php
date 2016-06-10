<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeployRecordRerun extends Model {

    protected $fillable = ['deploy_id', 'delivers', 'opens', 'clicks'];
    public $timestamps = false;
  
    public function report() {
        return $this->belongsTo('App\Model\StandardReport');
    }
}