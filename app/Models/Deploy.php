<?php

namespace App\Models;

use App\Models\ModelTraits\ModelCacheControl;
use Illuminate\Database\Eloquent\Model;

class Deploy extends Model
{
    protected $guarded = [];
    protected $hidden = array('created_at', 'updated_at');
    const PENDING_PACKAGE_STATUS = 2;
    const VERIFIED_PACKAGE_STATUS = 3;
    const NO_PACKAGE_STATUS = 0;
    const CREATED_PACKAGE_STATUS = 1;
    use ModelCacheControl;

    public function ListProfiles(){
        return $this->belongsToMany('App\Models\MT1Models\ListProfile',"list_profile","profile_id");
    }

    public function espAccount(){
        return $this->belongsTo('App\Models\EspAccount');

    }

    public function offer(){
        return $this->belongsTo('App\Models\Offer');
    }

    public function mailingDomain(){
        return $this->belongsTo('App\Models\Domain');
    }

    public function mailingTemplate(){
        return $this->belongsTo('App\Models\MailingTemplate', 'template_id', 'id');
    }

}
