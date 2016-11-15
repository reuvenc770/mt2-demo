<?php
/**
 * @author Adam Chin <achin@zetaglobal.net>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RawFeedEmailFailed extends Model
{
    protected $guarded = [ 'id' ];
    protected $table = 'raw_feed_email_failed';
}
