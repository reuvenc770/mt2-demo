<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\EmailActionAggregation
 *
 * @property int $email_id
 * @property int $deploy_id
 * @property string $date
 * @property int $deliveries
 * @property int $opens
 * @property int $clicks
 * @property int $conversions
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailActionAggregation whereClicks($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailActionAggregation whereConversions($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailActionAggregation whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailActionAggregation whereDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailActionAggregation whereDeliveries($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailActionAggregation whereDeployId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailActionAggregation whereEmailId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailActionAggregation whereOpens($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailActionAggregation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class EmailActionAggregation extends Model
{
    protected $connection = 'list_profile';
    protected $guarded = [];
}
