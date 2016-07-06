<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Models;

use App\Models\Interfaces\IScheduledFilter;
use Illuminate\Database\Eloquent\Model;

class AttributionActivitySchedule extends Model implements IScheduledFilter
{
    protected $connection = 'attribution';
    protected $primaryKey = "email_id";
    public function record () {
        return $this->belongsTo( 'emails' , 'email_id' , 'id' );
    }
}
