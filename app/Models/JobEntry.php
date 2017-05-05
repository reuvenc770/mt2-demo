<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\JobEntry
 *
 * @property int $id
 * @property string $job_name
 * @property string $account_name
 * @property string $account_number
 * @property int $campaign_id
 * @property string $time_started
 * @property string $time_finished
 * @property int $rows_impacted
 * @property int $attempts
 * @property string $tracking
 * @property string $status
 * @property string $time_fired
 * @property mediumint $runtime_seconds_threshold
 * @property string $acceptance_test
 * @property json $diagnostics
 * @method static \Illuminate\Database\Query\Builder|\App\Models\JobEntry whereAccountName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\JobEntry whereAccountNumber($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\JobEntry whereAttempts($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\JobEntry whereCampaignId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\JobEntry whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\JobEntry whereJobName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\JobEntry whereRowsImpacted($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\JobEntry whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\JobEntry whereTimeFinished($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\JobEntry whereTimeFired($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\JobEntry whereTimeStarted($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\JobEntry whereTracking($value)
 * @mixin \Eloquent
 */
class JobEntry extends Model
{
    CONST RUNNING = 1;
    CONST SUCCESS = 2;
    CONST FAILED = 3;
    CONST WAITING = 4;
    CONST ONQUEUE = 5;
    CONST SKIPPED = 6;
    CONST RUNTIME_WARNING = 7;
    CONST RUNTIME_FAILED = 8;
    CONST ACCEPTANCE_TEST_FAILED = 9;
    protected $guarded = ['id'];
    public $timestamps = false;



    public static function getPrettyStatusNames(){
        return array(
            self::ONQUEUE => "On Queue",
            self::RUNNING => "Running",
            self::SUCCESS => "Successful",
            self::FAILED  => "Failed",
            self::WAITING  => "Waiting",
            self::SKIPPED  => "Lock Skip",
            self::RUNTIME_WARNING => 'Running - Possibly Hanging',
            self::RUNTIME_FAILED => 'Runtime Threshold Exceeded - Non-fatal Error',
            self::ACCEPTANCE_TEST_FAILED => 'Completed - Failed Acceptance Test'
        );
    }

}
