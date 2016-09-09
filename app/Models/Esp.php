<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Esp extends Model
{

    public function espAccounts()
    {
        return $this->hasMany('App\Models\EspAccount');
    }

    public function accountMapping()
    {
        return $this->hasOne('App\Models\EspCampaignMapping');
    }

    public function deliverableCsvMapping() {
        return $this->hasOne('App\Models\DeliverableCsvMapping');
    }

    public function suppressionReasons(){
        return $this->hasMany('App\Models\SuppressionReason');
    }

    public function fieldOptions() {
        return $this->hasOne('App\Models\EspFieldOption');
    }
}
