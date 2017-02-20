<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ListProfileSchedule
 *
 * @property int $id
 * @property int $list_profile_id
 * @property bool $run_daily
 * @property bool $run_weekly
 * @property string $day_of_week
 * @property bool $run_monthly
 * @property bool $day_of_month
 * @property string $last_run
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\ListProfile $listProfile
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileSchedule whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileSchedule whereDayOfMonth($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileSchedule whereDayOfWeek($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileSchedule whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileSchedule whereLastRun($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileSchedule whereListProfileId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileSchedule whereRunDaily($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileSchedule whereRunMonthly($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileSchedule whereRunWeekly($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileSchedule whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ListProfileSchedule extends Model
{
    protected $guarded = ['id'];
    protected $connection = 'list_profile';

    public function listProfile () {
        return $this->belongsTo( 'App\Models\ListProfile' );
    }
}
