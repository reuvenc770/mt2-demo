<?php

namespace App\Models;

use App\Models\ModelTraits\ModelCacheControl;
use Illuminate\Database\Eloquent\Model;

class Deploy extends Model
{
    protected $guarded = [];
    protected $hidden = array('created_at', 'updated_at');
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
