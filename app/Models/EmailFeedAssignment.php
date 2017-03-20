<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\EmailFeedAssignment
 *
 * @property int $email_id
 * @property int $feed_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $capture_date
 * @property-read \App\Models\Email $email
 * @property-read \App\Models\Feed $feed
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EmailFeedAssignmentHistory[] $history
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailFeedAssignment whereCaptureDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailFeedAssignment whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailFeedAssignment whereEmailId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailFeedAssignment whereFeedId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailFeedAssignment whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class EmailFeedAssignment extends Model
{
    const LIVE_TABLE_NAME = 'email_feed_assignments';
    const BASE_TABLE_NAME = 'email_feed_assignments_model_';

    protected $connection = 'attribution';
    protected $fillable = ['email_id', 'feed_id', 'subscribe_date'];
    protected $primaryKey = "email_id";


    public function email () {
        return $this->belongsTo( 'App\Models\Email' );
    }

    public function feed () {
        return $this->belongsTo( 'App\Models\Feed' );
    }

    public function history () {
        return $this->hasMany( 'App\Models\EmailFeedAssignmentHistory' );
    }

    public function setLiveTable () {
        $this->setTable( self::LIVE_TABLE_NAME );
    }

    public function setModelTable ( $modelId ) {
        if ( $modelId > 0 ) {
            $this->setTable( self::BASE_TABLE_NAME . $modelId );
        }
    }
}
