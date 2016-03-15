<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Storage;
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

    public function aweberReport(){
        return $this->hasMany('App\Models\AWeberReport');
    }

    public function getResponseReport(){
        return $this->hasMany('App\Models\GetResponseReport');
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


    protected static function boot() {
        parent::boot();
        static::Created(function(EspAccount $account) {
            $startingPath = $account->account_name."/";
            Storage::makeDirectory($startingPath);
            Storage::makeDirectory($startingPath."clicks");
            Storage::makeDirectory($startingPath."complaints");
            Storage::makeDirectory($startingPath."delivered");
            Storage::makeDirectory($startingPath."opens");
            Storage::makeDirectory($startingPath."unsubscribes");
            Storage::makeDirectory($startingPath."campaigns");
        });

    }

}
