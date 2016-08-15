<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailFeedAssignmentHistory extends Model
{
    protected $connection = 'attribution';
    protected $fillable = ['email_id', 'prev_feed_id', 'new_feed_id', 'created_at', 'updated_at'];
    protected $primaryKey = "email_id";

    public function email () {
        return $this->hasOne( 'App\Models\Email' );
    }

    public function feeds () {
        return $this->hasMany( 'App\Models\Feed' );
    }

    public function assignment () {
        return $this->belongsTo( 'App\Models\EmailFeedAssignment' );
    }
}
