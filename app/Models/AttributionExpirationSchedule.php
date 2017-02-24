<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Models;

use App\Models\Interfaces\IScheduledFilter;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AttributionExpirationSchedule
 *
 * @property int $email_id
 * @property string $trigger_date
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\Email $record
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AttributionExpirationSchedule whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AttributionExpirationSchedule whereEmailId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AttributionExpirationSchedule whereTriggerDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AttributionExpirationSchedule whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AttributionExpirationSchedule extends Model implements IScheduledFilter
{
    protected $connection = 'attribution';
    protected $primaryKey = "email_id";
    protected $guarded = [''];
    protected $fillable = ['email_id', 'trigger_date', 'created_at', 'updated_at'];
    
    public function record () {
        return $this->belongsTo( 'App\Models\Email' , 'email_id' , 'id' );
    }
}
