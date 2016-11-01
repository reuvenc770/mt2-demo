<?php

namespace App\Models;

use App\Models\ModelTraits\ModelCacheControl;

use Illuminate\Database\Eloquent\Model;

class Feed extends Model {
    use ModelCacheControl;

    protected $guarded = [];

    public function emailFeedInstances() {
        return $this->hasMany('App\Models\EmailFeedInstance');
    }

    public function attributionLevel() {
        return $this->hasOne('App\Models\AttributionLevel');
    }

    public function client() {
        return $this->belongsTo('App\Models\Client');
    }

    public function feedGroups () {
        return $this->belongsToMany( 'App\Models\FeedGroup' , 'feedgroup_feed' );
    }

    public function suppressionList() {
        return $this->belongsTo('App\Models\SuppressionList');
    }
}
