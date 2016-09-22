<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailFeedAssignment extends Model
{
    const LIVE_TABLE_NAME = 'email_feed_assignments';
    const BASE_TABLE_NAME = 'email_feed_assignments_model_';

    protected $connection = 'attribution';
    protected $fillable = ['email_id', 'feed_id', 'capture_date'];
    protected $primaryKey = "email_id";


    public function email () {
        return $this->hasOne( 'App\Models\Email' );
    }

    public function feed () {
        return $this->hasOne( 'App\Models\Feed' );
    }

    public function history () {
        return $this->hasMany( 'App\Models\EmailFeedAssignmentHistory' );
    }

    public function setLiveTable () {
        $this->setTable( self::LIVE_TABLE_NAME );
    }

    public function setModelTable ( $modelId ) {
        if ( $modelId > 0 ) {
            $this->table = self::BASE_TABLE_NAME . $modelId;
        }
    }
}
