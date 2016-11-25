<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ModelTraits\ModelCacheControl;
use App\Models\ListProfileCombine;
class ListProfile extends Model
{
    use ModelCacheControl;

    protected $guarded = [''];
    public $timestamps = false;
    protected $connection = 'list_profile';

    public function clients() {
        return $this->belongsToMany('App\Models\Client', 'list_profile.list_profile_clients');
    }

    public function feeds() {
        return $this->belongsToMany('App\Models\Feed', 'list_profile.list_profile_feeds');
    }
    public function feedGroups() {
        return $this->belongsToMany('App\Models\FeedGroup', 'list_profile.list_profile_feed_groups');
    }

    public function domainGroups() {
        return $this->belongsToMany('App\Models\DomainGroup', 'list_profile.list_profile_domain_groups');
    }

    public function offers() {
        return $this->belongsToMany('App\Models\Offer', 'list_profile.list_profile_offers');
    }

    public function verticals() {
        return $this->belongsToMany('App\Models\CakeVertical', 'list_profile.list_profile_verticals');
    }

    public function countries () {
        return $this->belongsToMany( 'App\Models\Country' , 'list_profile.list_profile_countries' );
    }

    public function schedule () {
        return $this->hasOne( 'App\Models\ListProfileSchedule' );
    }


    /**
     * Because Deploys now can choose a single list profile or a list combine we have to know the difference
     * and following a small pattern levelocity has instituted we are creating a list combine for every list profile
     * this is so deploys can query one table and save a single value, removing extra logic from deploys
     *
     */
    public static function boot()
    {
        parent::boot();

        static::created(function($listProfile){
            $listCombine = new ListProfileCombine();
            $listCombine->name = $listProfile->name;
            $listCombine->list_profile_id = $listProfile->id;
            $listCombine->save();
            $listCombine->listProfiles()->attach($listProfile->id);
        });

        static::updated(function($listProfile){
            $listCombine = ListProfileCombine::where('list_profile_id', $listProfile->id)->first();
            $listCombine->name = $listProfile->name;
            $listCombine->save();
        });
    }

}
