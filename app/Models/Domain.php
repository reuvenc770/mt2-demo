<?php

namespace App\Models;

use App\Models\ModelTraits\ModelCacheControl;
use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    use ModelCacheControl;
    CONST MAILING_DOMAIN = 1;
    CONST CONTENT_DOMAIN = 2;
    protected $guarded = ['id'];
    public $timestamps = false;

    public function espAccount(){
        return $this->hasOne('App\Models\EspAccount');
    }

    public function esp(){
        return $this->hasOne('App\Models\Esp');
    }
    public function proxy(){
        return $this->hasOne('App\Models\Proxy');
    }

}
