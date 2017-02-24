<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SuppressionGlobalOrange
 *
 * @property int $id
 * @property string $email_address
 * @property string $suppress_datetime
 * @property int $reason_id
 * @property int $type_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SuppressionGlobalOrange whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SuppressionGlobalOrange whereEmailAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SuppressionGlobalOrange whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SuppressionGlobalOrange whereReasonId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SuppressionGlobalOrange whereSuppressDatetime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SuppressionGlobalOrange whereTypeId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SuppressionGlobalOrange whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SuppressionGlobalOrange extends Model
{
    protected $guarded = [ '' ];
    protected $connection = 'suppression';
    protected $table = 'suppression_global_orange';
}
