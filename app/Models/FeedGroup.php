<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Models;

use App\Models\ModelTraits\ModelCacheControl;
use Illuminate\Database\Eloquent\Model;

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
