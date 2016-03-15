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

    public function maroReport() {
        return $this->hasMany('App\Models\MaroReport');
    }

    public function ymlpReport() {
        return $this->hasMany('App\Models\YmlpReport');
    }

    public function getFirstKey()
    {
        return $this->attributes['key_1'];
    }

    public function getSecondKey()
    {
        return $this->attributes['key_2'];
    }

    public function deliverableMapping() {
        return $this->hasOne('App\Model\DeliverableCsvMapping');
    }

}
