<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\DeployActionEntry
 *
 * @property int $id
 * @property int $esp_account_id
 * @property int $esp_internal_id
 * @property string $last_success_click
 * @property string $last_fail_click
 * @property string $last_success_open
 * @property string $last_fail_open
 * @property string $last_success_deliverable
 * @property string $last_fail_deliverable
 * @property string $last_success_optout
 * @property string $last_fail_optout
 * @property string $last_success_bounce
 * @property string $last_fail_bounce
 * @property string $last_success_complaint
 * @property string $last_fail_complaint
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DeployActionEntry whereEspAccountId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DeployActionEntry whereEspInternalId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DeployActionEntry whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DeployActionEntry whereLastFailBounce($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DeployActionEntry whereLastFailClick($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DeployActionEntry whereLastFailComplaint($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DeployActionEntry whereLastFailDeliverable($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DeployActionEntry whereLastFailOpen($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DeployActionEntry whereLastFailOptout($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DeployActionEntry whereLastSuccessBounce($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DeployActionEntry whereLastSuccessClick($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DeployActionEntry whereLastSuccessComplaint($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DeployActionEntry whereLastSuccessDeliverable($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DeployActionEntry whereLastSuccessOpen($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DeployActionEntry whereLastSuccessOptout($value)
 * @mixin \Eloquent
 */
class DeployActionEntry extends Model
{
    protected $guarded = ['id'];
    public $timestamps = false;

}
