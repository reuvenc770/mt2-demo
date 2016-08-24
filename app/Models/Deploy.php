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

    // The next two are stored in the same table
    public function mailingDomain() {
        return $this->belongsTo('App\Models\Domain', 'mailing_domain_id');
    }

    public function contentDomain() {
        return $this->belongsTo('App\Models\Domain', 'content_domain_id');
    }

    public function mailingTemplate() {
        return $this->belongsTo('App\Models\MailingTemplate', 'template_id', 'id');
    }

    public function creative() {
        return $this->belongsTo('App\Models\Creative');
    }

    public function from() {
        return $this->belongsTo('App\Models\From');
    }

    public function subject() {
        return $this->belongsTo('App\Models\Subject');
    }

}
