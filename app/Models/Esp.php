<?php

namespace App\Models;

use App\Models\ModelTraits\Deletable;
use App\Models\ModelTraits\ModelCacheControl;
use Illuminate\Database\Eloquent\Model;

class Esp extends Model
{
    use ModelCacheControl;
    use Deletable;
    protected $guarded = ['id'];

    public function espAccounts()
    {
        return $this->hasMany('App\Models\EspAccount');
    }

    public function accountMapping()
    {
        return $this->hasOne('App\Models\EspCampaignMapping');
    }

    public function suppressionReasons(){
        return $this->hasMany('App\Models\SuppressionReason');
    }

    public function fieldOptions() {
        return $this->hasOne('App\Models\EspFieldOption');
    }
}
