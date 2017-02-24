<?php

namespace App\Models;

use App\Models\Interfaces\IReport;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CakeData
 *
 * @property int $id
 * @property string $subid_1
 * @property string $subid_2
 * @property int $email_id
 * @property string $subid_4
 * @property string $subid_5
 * @property int $affiliate_id
 * @property string $user_agent_string
 * @property int $clicks
 * @property int $conversions
 * @property float $revenue
 * @property string $clickDate
 * @property string $campaignDate
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CakeData whereAffiliateId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CakeData whereCampaignDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CakeData whereClickDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CakeData whereClicks($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CakeData whereConversions($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CakeData whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CakeData whereEmailId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CakeData whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CakeData whereRevenue($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CakeData whereSubid1($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CakeData whereSubid2($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CakeData whereSubid4($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CakeData whereSubid5($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CakeData whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CakeData whereUserAgentString($value)
 * @mixin \Eloquent
 */
class CakeData extends Model implements IReport
{
    protected $guarded = ['id'];
    protected $table = 'cake_aggregated_data';
    protected $connection = "reporting_data";
}