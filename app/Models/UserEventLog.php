<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserEventLog
 *
 * @property int $id
 * @property int $user_id
 * @property string $page
 * @property string $action
 * @property int $status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $ip_address
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserEventLog whereAction($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserEventLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserEventLog whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserEventLog whereIpAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserEventLog wherePage($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserEventLog whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserEventLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserEventLog whereUserId($value)
 * @mixin \Eloquent
 */
class UserEventLog extends Model
{
   CONST SUCCESS = 1;
   CONST FAILED = 2;
   CONST VALIDATION_FAILED = 3;
   CONST UNAUTHORIZED = 4;
   CONST ERROR = 5;
   protected $guarded = ['id'];
}
