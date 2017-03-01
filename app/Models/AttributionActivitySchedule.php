<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Models;

use App\Models\Interfaces\IScheduledFilter;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AttributionActivitySchedule
 *
 * @property int $email_id
 * @property string $trigger_date
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\Email $record
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AttributionActivitySchedule whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AttributionActivitySchedule whereEmailId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AttributionActivitySchedule whereTriggerDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AttributionActivitySchedule whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AttributionActivitySchedule extends Model implements IScheduledFilter
{
    protected $connection = 'attribution';
    protected $primaryKey = "email_id";
    protected $guarded = [''];
    protected $fillable = ['email_id', 'trigger_date', 'created_at', 'updated_at'];
    
    public function record () {
        return $this->belongsTo( 'App\Models\Email' , 'email_id' , 'id' );
    }
}
