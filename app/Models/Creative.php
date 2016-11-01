<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ModelTraits\Mailable;

class Creative extends Model
{
    use Mailable;

    protected $guarded = [];
    const DEFAULT_DOMAIN = "wealthpurse.com";

    public function deploys() {
        return $this->hasMany('App\Models\Deploys');
    }

    public function clickthroughRate() {
        return $this->hasOne('App\Models\CreativeClickthroughRate');
    }
}
