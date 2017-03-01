<?php

namespace App\Models;

use App\Models\Interfaces\IReportMapper;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AWeberReport
 *
 * @property int $id
 * @property int $esp_account_id
 * @property string $deploy_id
 * @property string $campaign_name
 * @property string $subject
 * @property string $internal_id
 * @property string $info_url
 * @property int $total_sent
 * @property int $total_opens
 * @property int $unique_opens
 * @property int $total_clicks
 * @property int $unique_clicks
 * @property int $total_unsubscribes
 * @property int $total_undelivered
 * @property string $datetime
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AWeberReport whereCampaignName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AWeberReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AWeberReport whereDatetime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AWeberReport whereDeployId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AWeberReport whereEspAccountId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AWeberReport whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AWeberReport whereInfoUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AWeberReport whereInternalId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AWeberReport whereSubject($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AWeberReport whereTotalClicks($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AWeberReport whereTotalOpens($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AWeberReport whereTotalSent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AWeberReport whereTotalUndelivered($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AWeberReport whereTotalUnsubscribes($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AWeberReport whereUniqueClicks($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AWeberReport whereUniqueOpens($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AWeberReport whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AWeberReport extends Model implements IReportMapper
{
    CONST UNIQUE_OPENS = 1;
    CONST UNIQUE_CLICKS = 2;
    protected $guarded = ['id'];
    protected $connection = "reporting_data";

    public function getDateFieldName(){
        return "datetime";
    }

    public function getSubjectFieldName(){
        return "subject";
    }
}
