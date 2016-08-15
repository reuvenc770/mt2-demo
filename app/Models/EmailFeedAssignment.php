<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailFeedAssignment extends Model
{
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
}
