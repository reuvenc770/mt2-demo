<?php

namespace App\Models;

use App\Facades\DeployActionEntry;
use App\Models\Interfaces\IReportMapper;
use Illuminate\Database\Eloquent\Model;
use Log;
/**
 * App\Models\StandardReport
 *
 * @property int $id
 * @property int $external_deploy_id
 * @property string $campaign_name
 * @property int $m_deploy_id
 * @property int $esp_account_id
 * @property int $esp_internal_id
 * @property string $datetime
 * @property int $m_creative_id
 * @property int $m_offer_id
 * @property string $name
 * @property string $subject
 * @property string $from
 * @property string $from_email
 * @property int $m_sent
 * @property int $e_sent
 * @property int $delivered
 * @property int $bounced
 * @property int $optouts
 * @property int $m_opens
 * @property int $e_opens
 * @property int $t_opens
 * @property int $m_opens_unique
 * @property int $e_opens_unique
 * @property int $t_opens_unique
 * @property int $m_clicks
 * @property int $e_clicks
 * @property int $t_clicks
 * @property int $m_clicks_unique
 * @property int $e_clicks_unique
 * @property int $t_clicks_unique
 * @property int $conversions
 * @property float $cost
 * @property float $revenue
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StandardReport whereBounced($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StandardReport whereCampaignName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StandardReport whereConversions($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StandardReport whereCost($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StandardReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StandardReport whereDatetime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StandardReport whereDelivered($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StandardReport whereEClicks($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StandardReport whereEClicksUnique($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StandardReport whereEOpens($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StandardReport whereEOpensUnique($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StandardReport whereESent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StandardReport whereEspAccountId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StandardReport whereEspInternalId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StandardReport whereExternalDeployId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StandardReport whereFrom($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StandardReport whereFromEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StandardReport whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StandardReport whereMClicks($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StandardReport whereMClicksUnique($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StandardReport whereMCreativeId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StandardReport whereMDeployId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StandardReport whereMOfferId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StandardReport whereMOpens($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StandardReport whereMOpensUnique($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StandardReport whereMSent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StandardReport whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StandardReport whereOptouts($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StandardReport whereRevenue($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StandardReport whereSubject($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StandardReport whereTClicks($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StandardReport whereTClicksUnique($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StandardReport whereTOpens($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StandardReport whereTOpensUnique($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StandardReport whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class StandardReport extends Model implements IReportMapper
{
    protected $guarded = ['id'];
    protected $connection = "reporting_data";

    //TODO  This should really be created during deploy process, but needs to be triggered now for dashboard
    public static function boot()
    {
        parent::boot();

        static::created(function($report)
        {
            DeployActionEntry::initDeployActions($report);
        });

    }

    public function beforeCreate()
    {
        $this->created_at = date("Y-m-d H:i:s");
        $this->updated_at = date("Y-m-d H:i:s");
    }

    public function getDateFieldName(){
        return "datetime";
    }

    public function getSubjectFieldName(){
        return "subject";
    }
}
