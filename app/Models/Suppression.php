<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Suppression extends Model
{
    CONST TYPE_UNSUB = 1;
    CONST TYPE_HARD_BOUNCE = 2;
    CONST TYPE_COMPLAINT = 3;
    protected $guarded = ['id'];

    public function espAccount() {
        return $this->belongsto('App\Models\EspAccount');
    }
    public function suppressionReason(){
        return $this->hasOne('App\Models\SuppressionReason', 'id', 'reason_id');
    }
    public function standardReport(){
        return $this->belongsTo('App\Models\StandardReport', 'esp_internal_id', 'esp_internal_id');
    }
    public function email(){
        return $this->belongsTo('App\Models\Email');
    }


}
