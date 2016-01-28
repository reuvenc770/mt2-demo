<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EspAccount extends Model
{
    public function esp()
    {
        return $this->belongsTo('App\Models\Esp');
    }

    public function accountMapping()
    {
        return $this->hasOne('App\Models\EspAccountMapping');
    }

    public function blueHornetReports(){
        return $this->hasMany('App\Models\BlueHornetReport');
    }

    public function campaignerReports(){
        return $this->hasMany('App\Models\CampaignerReport');
    }

    public function emailDirectReport(){
    return $this->hasMany('App\Models\EmailDirectReport');
    }

    public function getFirstKey()
    {
        return $this->attributes['key_1'];
    }

    public function getSecondKey()
    {
        return $this->attributes['key_2'];
    }

}
