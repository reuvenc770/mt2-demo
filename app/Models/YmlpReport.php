<?php

namespace App\Models;

use App\Models\Interfaces\IReportMapper;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\YmlpReport
 *
 * @property int $id
 * @property int $esp_account_id
 * @property int $internal_id
 * @property string $name
 * @property string $from_name
 * @property string $from_email
 * @property string $subject
 * @property string $date
 * @property string $groups
 * @property string $filters
 * @property int $recipients
 * @property int $delivered
 * @property int $bounced
 * @property int $total_opens
 * @property int $unique_opens
 * @property int $total_clicks
 * @property int $unique_clicks
 * @property float $open_rate
 * @property float $click_through_rate
 * @property string $forwards
 * @property string $permalink
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\YmlpReport whereBounced($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\YmlpReport whereClickThroughRate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\YmlpReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\YmlpReport whereDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\YmlpReport whereDelivered($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\YmlpReport whereEspAccountId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\YmlpReport whereFilters($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\YmlpReport whereForwards($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\YmlpReport whereFromEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\YmlpReport whereFromName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\YmlpReport whereGroups($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\YmlpReport whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\YmlpReport whereInternalId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\YmlpReport whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\YmlpReport whereOpenRate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\YmlpReport wherePermalink($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\YmlpReport whereRecipients($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\YmlpReport whereSubject($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\YmlpReport whereTotalClicks($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\YmlpReport whereTotalOpens($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\YmlpReport whereUniqueClicks($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\YmlpReport whereUniqueOpens($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\YmlpReport whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class YmlpReport extends Model implements IReportMapper {
    protected $guarded = ['id'];
    protected $connection = "reporting_data";

    public function getDateFieldName(){
        return "date";
    }

    public function getSubjectFieldName(){
        return "subject";
    }
}
