<?php

namespace App\Models;

use App\Models\Interfaces\IReportMapper;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\EmailDirectReport
 *
 * @property int $id
 * @property int $internal_id
 * @property int $campaign_id
 * @property string $name
 * @property string $status
 * @property int $is_active
 * @property string $created
 * @property string $schedule_date
 * @property string $from_name
 * @property string $from_email
 * @property string $to_name
 * @property int $creative_id
 * @property string $target
 * @property string $subject
 * @property string $archive_url
 * @property int $emails_sent
 * @property int $opens
 * @property int $unique_clicks
 * @property int $total_clicks
 * @property int $removes
 * @property int $forwards
 * @property int $forwards_from
 * @property int $hard_bounces
 * @property int $soft_bounces
 * @property int $complaints
 * @property int $delivered
 * @property float $delivery_rate
 * @property float $open_rate
 * @property float $unique_rate
 * @property float $ctr
 * @property float $remove_rate
 * @property float $bounce_rate
 * @property float $soft_bounce_rate
 * @property float $complaint_rate
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $esp_account_id
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailDirectReport whereArchiveUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailDirectReport whereBounceRate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailDirectReport whereCampaignId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailDirectReport whereComplaintRate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailDirectReport whereComplaints($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailDirectReport whereCreated($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailDirectReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailDirectReport whereCreativeId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailDirectReport whereCtr($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailDirectReport whereDelivered($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailDirectReport whereDeliveryRate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailDirectReport whereEmailsSent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailDirectReport whereEspAccountId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailDirectReport whereForwards($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailDirectReport whereForwardsFrom($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailDirectReport whereFromEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailDirectReport whereFromName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailDirectReport whereHardBounces($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailDirectReport whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailDirectReport whereInternalId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailDirectReport whereIsActive($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailDirectReport whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailDirectReport whereOpenRate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailDirectReport whereOpens($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailDirectReport whereRemoveRate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailDirectReport whereRemoves($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailDirectReport whereScheduleDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailDirectReport whereSoftBounceRate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailDirectReport whereSoftBounces($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailDirectReport whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailDirectReport whereSubject($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailDirectReport whereTarget($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailDirectReport whereToName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailDirectReport whereTotalClicks($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailDirectReport whereUniqueClicks($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailDirectReport whereUniqueRate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailDirectReport whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class EmailDirectReport extends Model implements IReportMapper
{
    protected $guarded = ['id'];
    protected $connection = "reporting_data";

    public function getDateFieldName(){
        return "scheduled_date";
    }

    public function getSubjectFieldName(){
        return "subject";
    }
}
