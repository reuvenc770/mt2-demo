<?php

namespace App\Models;

use App\Models\ModelTraits\ModelCacheControl;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ListProfileCombine
 *
 * @property int $id
 * @property string $name
 * @property int $list_profile_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property bool $party
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Deploy[] $deploys
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ListProfile[] $listProfiles
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileCombine whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileCombine whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileCombine whereListProfileId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileCombine whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileCombine whereParty($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileCombine whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ListProfileCombine extends Model
{
    use ModelCacheControl;
    protected $guarded = ['id'];
    protected $connection = 'list_profile';

    public function listProfiles()
    {
        return $this->belongsToMany('App\Models\ListProfile');
    }

    public function deploys () {
        return $this->hasMany( 'App\Models\Deploy' );
    }
}
