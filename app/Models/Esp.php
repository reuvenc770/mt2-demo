<?php

namespace App\Models;

use App\Models\ModelTraits\ModelCacheControl;
use Illuminate\Database\Eloquent\Model;

class Esp extends Model
{
    use ModelCacheControl;
    protected $guarded = ['id'];

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
