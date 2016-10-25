<?php
/**
 * @author Adam Chin <achin@zetaglobal.com> 
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeedGroupFeed extends Model {
    protected $table = 'feedgroup_feed';
    protected $guarded = [ 'feedgroup_id' ];

    public function feedGroup () {
        return $this->belongsTo( 'App\Models\FeedGroup' );
    }
}
