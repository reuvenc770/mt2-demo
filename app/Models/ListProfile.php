<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ModelTraits\ModelCacheControl;
use App\Models\ModelTraits\Deletable;
use App\Models\ListProfileCombine;
use App\Models\ListProfileCombinePivot;

class ListProfile extends Model
{
    use ModelCacheControl;
    use Deletable;

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

    public function schedule () {
        return $this->hasOne( 'App\Models\ListProfileSchedule' );
    }

    public function baseCombine () {
        return $this->hasOne( 'App\Models\ListProfileCombine' , 'list_profile_id' , 'id' );
    }

    public function combines () {
        return $this->belongsToMany( 'App\Models\ListProfileCombine' , 'list_profile.list_profile_list_profile_combine' );
    }

    public function deploys () {
        $combines = $this->combines();
        $deploys = [];

        if( $combines->count() <= 0 ) {
            return collect( [] );
        }

        $combines->get()->each( function ( $item , $key ) use ( &$deploys ) {
            if ( $item->deploys()->count() > 0 ) {
                $item->deploys()->each( function ( $current , $currentKey ) use ( &$deploys ) {
                    array_push( $deploys , $current );
                } );
            }
        } );

        return collect( $deploys );
    }

    public function canModelBeDeleted () {
        return $this->deploys()->isEmpty();
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

        static::deleted(function($listProfile){
            $combineIDs = $listProfile->baseCombine()->pluck( 'id' )->toArray();
            $listProfileId = $listProfile->id;

            if ( $listProfile->clients()->count() ) { $listProfile->clients()->detach(); }
            if ( $listProfile->feeds()->count() ) { $listProfile->feeds()->detach(); }
            if ( $listProfile->feedGroups()->count() ) { $listProfile->feedGroups()->detach(); }
            if ( $listProfile->domainGroups()->count() ) { $listProfile->domainGroups()->detach(); }
            if ( $listProfile->offers()->count() ) { $listProfile->offers()->detach(); }
            if ( $listProfile->verticals()->count() ) { $listProfile->verticals()->detach(); }
            
            $listProfile->baseCombine()->delete();
            $listProfile->schedule()->delete();

            ListProfileCombinePivot::where( 'list_profile_id' , $listProfileId )->delete();
            ListProfileCombinePivot::whereIn( 'list_profile_combine_id' , $combineIDs )->delete();
        });
    }

}
