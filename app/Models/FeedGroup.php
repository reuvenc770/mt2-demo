<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Models;

use App\Models\ModelTraits\ModelCacheControl;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FeedGroup
 *
 * @property int $id
 * @property string $name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\FeedGroupFeed[] $feedGroupFeeds
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Feed[] $feeds
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FeedGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FeedGroup whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FeedGroup whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FeedGroup whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FeedGroup extends Model
{
    use ModelCacheControl;

    protected $guarded = [ '' ];

    public function feeds () {
        return $this->belongsToMany( 'App\Models\Feed' , 'feedgroup_feed' , 'feedgroup_id' , 'feed_id' );
    }

    public function feedGroupFeeds () {
        return $this->hasMany( 'App\Models\FeedGroupFeed' , 'feedgroup_id' , 'id' );
    }
}
