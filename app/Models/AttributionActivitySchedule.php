<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributionActivitySchedule extends Model
{
    protected $connection = 'attribution';

    public function record () {
        return $this->belongsTo( 'emails' , 'email_id' , 'id' );
    }
}
