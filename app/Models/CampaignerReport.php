<?php

namespace App\Models;

use App\Models\Interfaces\IReportMapper;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CampaignerReport
 *
 * @property int $id
 * @property int $internal_id
 * @property string $name
 * @property string $subject
 * @property string $from_name
 * @property string $from_email
 * @property string $run_on
 * @property int $sent
 * @property int $delivered
 * @property int $hard_bounces
 * @property int $soft_bounces
 * @property int $spam_bounces
 * @property int $opens
 * @property int $clicks
 * @property int $unsubs
 * @property int $spam_complaints
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $esp_account_id
 * @property int $run_id
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CampaignerReport whereClicks($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CampaignerReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CampaignerReport whereDelivered($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CampaignerReport whereEspAccountId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CampaignerReport whereFromEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CampaignerReport whereFromName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CampaignerReport whereHardBounces($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CampaignerReport whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CampaignerReport whereInternalId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CampaignerReport whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CampaignerReport whereOpens($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CampaignerReport whereRunId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CampaignerReport whereRunOn($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CampaignerReport whereSent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CampaignerReport whereSoftBounces($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CampaignerReport whereSpamBounces($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CampaignerReport whereSpamComplaints($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CampaignerReport whereSubject($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CampaignerReport whereUnsubs($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CampaignerReport whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CampaignerReport extends Model implements IReportMapper
{
    protected $guarded = ['id'];
    protected $connection = "reporting_data";

    public function getDateFieldName(){
        return "run_on";
    }

    public function getSubjectFieldName(){
        return "subject";
    }
}
