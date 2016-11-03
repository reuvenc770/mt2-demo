<?php

namespace App\Models;

use App\Models\ModelTraits\ModelCacheControl;
use Illuminate\Database\Eloquent\Model;
use Storage;
class EspAccount extends Model
{
    use ModelCacheControl;
    protected $guarded = ['id'];
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

    public function suppressions()
    {
        return $this->hasMany('App\Models\Suppression');
    }

    public function rerunDeploys() {
        return $this->hasMany('App\Models\DeployRecordRerun');
    }

    public function mailingTemplate()
    {
        return $this->belongsToMany('App\Models\MailingTemplate');
    }

    public function domains(){
        return $this->hasMany('App\Models\Domains');
    }

    public function imageLinkFormat() {
        return $this->hasOne('App\Models\EspAccountImageLinkFormat');
    }

}
