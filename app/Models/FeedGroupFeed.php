<?php
/**
 * @author Adam Chin <achin@zetaglobal.com> 
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FeedGroupFeed
 *
 * @property int $feedgroup_id
 * @property int $feed_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\FeedGroup $feedGroup
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FeedGroupFeed whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FeedGroupFeed whereFeedId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FeedGroupFeed whereFeedgroupId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FeedGroupFeed whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FeedGroupFeed extends Model {
    protected $table = 'feedgroup_feed';
    protected $guarded = [ 'feedgroup_id' ];

    public function feedGroup () {
        return $this->belongsTo( 'App\Models\FeedGroup' );
    }
}
