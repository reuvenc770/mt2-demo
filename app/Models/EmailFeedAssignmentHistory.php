<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\EmailFeedAssignmentHistory
 *
 * @property int $email_id
 * @property int $prev_feed_id
 * @property int $new_feed_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\EmailFeedAssignment $assignment
 * @property-read \App\Models\Email $email
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Feed[] $feeds
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailFeedAssignmentHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailFeedAssignmentHistory whereEmailId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailFeedAssignmentHistory whereNewFeedId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailFeedAssignmentHistory wherePrevFeedId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailFeedAssignmentHistory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
