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

    public function deliverableMapping() {
        return $this->hasOne('App\Model\DeliverableCsvMapping');
    }

    protected static function boot() {
        parent::boot();
        $array = array('clicks', 'complaints', 'delivered', 'opens', 'unsubscribes', 'campaigns');
        static::Created(function(EspAccount $account) use ($array) {
            $startingPath = $account->account_name."/";
            Storage::makeDirectory($startingPath);
            foreach ($array as $action){
                Storage::makeDirectory($action);
            }
        });

    }

}
