<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ModelTraits\ModelCacheControl;
use App\Models\ModelTraits\Deletable;
use App\Models\ListProfileCombine;
use App\Models\ListProfileCombinePivot;

/**
 * App\Models\ListProfile
 *
 * @property int $id
 * @property string $name
 * @property bool $admiral_only
 * @property int $deliverable_start
 * @property int $deliverable_end
 * @property int $openers_start
 * @property int $openers_end
 * @property int $open_count
 * @property int $clickers_start
 * @property int $clickers_end
 * @property int $click_count
 * @property int $converters_start
 * @property int $converters_end
 * @property int $conversion_count
 * @property bool $use_global_suppression
 * @property mixed $age_range
 * @property mixed $gender
 * @property mixed $zip
 * @property mixed $city
 * @property mixed $state
 * @property mixed $device_type
 * @property mixed $mobile_carrier
 * @property bool $insert_header
 * @property int $total_count
 * @property mixed $device_os
 * @property mixed $feeds_suppressed
 * @property mixed $columns
 * @property string $run_frequency
 * @property string $created_at
 * @property string $updated_at
 * @property int $country_id
 * @property bool $party
 * @property-read \App\Models\ListProfileCombine $baseCombine
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Client[] $clients
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ListProfileCombine[] $combines
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Deploy[] $deploys
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\DomainGroup[] $domainGroups
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\FeedGroup[] $feedGroups
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Feed[] $feeds
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Offer[] $offers
 * @property-read \App\Models\ListProfileSchedule $schedule
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CakeVertical[] $verticals
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfile whereAdmiralOnly($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfile whereAgeRange($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfile whereCity($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfile whereClickCount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfile whereClickersEnd($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfile whereClickersStart($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfile whereColumns($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfile whereConversionCount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfile whereConvertersEnd($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfile whereConvertersStart($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfile whereCountryId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfile whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfile whereDeliverableEnd($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfile whereDeliverableStart($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfile whereDeviceOs($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfile whereDeviceType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfile whereFeedsSuppressed($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfile whereGender($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfile whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfile whereInsertHeader($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfile whereMobileCarrier($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfile whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfile whereOpenCount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfile whereOpenersEnd($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfile whereOpenersStart($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfile whereParty($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfile whereRunFrequency($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfile whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfile whereTotalCount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfile whereUseGlobalSuppression($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfile whereZip($value)
 * @mixin \Eloquent
 */
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
        return $this->hasManyThrough('App\Models\Deploy', 'App\Models\ListProfileCombinePivot', 'list_profile_id', 'list_profile_combine_id' );
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

            $listProfile->clients()->detach();
            $listProfile->feeds()->detach();
            $listProfile->feedGroups()->detach();
            $listProfile->domainGroups()->detach();
            $listProfile->offers()->detach();
            $listProfile->verticals()->detach();
            
            $listProfile->baseCombine()->delete();
            $listProfile->schedule()->delete();

            ListProfileCombinePivot::where( 'list_profile_id' , $listProfileId )->delete();
            ListProfileCombinePivot::whereIn( 'list_profile_combine_id' , $combineIDs )->delete();
        });
    }

}
