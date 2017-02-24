<?php

namespace App\Models;

use App\Models\ModelTraits\ModelCacheControl;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Feed
 *
 * @property int $id
 * @property int $client_id
 * @property string $name
 * @property bool $party
 * @property string $short_name
 * @property string $password
 * @property string $status
 * @property int $vertical_id
 * @property string $frequency
 * @property int $type_id
 * @property int $country_id
 * @property string $source_url
 * @property int $suppression_list_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\AttributionLevel $attributionLevel
 * @property-read \App\Models\Client $client
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EmailFeedInstance[] $emailFeedInstances
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\FeedGroup[] $feedGroups
 * @property-read \App\Models\SuppressionList $suppressionList
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Feed whereClientId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Feed whereCountryId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Feed whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Feed whereFrequency($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Feed whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Feed whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Feed whereParty($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Feed wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Feed whereShortName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Feed whereSourceUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Feed whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Feed whereSuppressionListId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Feed whereTypeId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Feed whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Feed whereVerticalId($value)
 * @mixin \Eloquent
 */
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
