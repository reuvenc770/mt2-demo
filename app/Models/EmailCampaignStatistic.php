<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\EmailCampaignStatistic
 *
 * @property int $id
 * @property int $email_id
 * @property int $campaign_id
 * @property string $last_status
 * @property string $esp_first_open_datetime
 * @property string $esp_last_open_datetime
 * @property int $esp_total_opens
 * @property string $esp_first_click_datetime
 * @property string $esp_last_click_datetime
 * @property int $esp_total_clicks
 * @property string $trk_first_open_datetime
 * @property string $trk_last_open_datetime
 * @property int $trk_total_opens
 * @property string $trk_first_click_datetime
 * @property string $trk_last_click_datetime
 * @property int $trk_total_clicks
 * @property string $mt_first_open_datetime
 * @property string $mt_last_open_datetime
 * @property int $mt_total_opens
 * @property string $mt_first_click_datetime
 * @property string $mt_last_click_datetime
 * @property int $mt_total_clicks
 * @property int $unsubscribed
 * @property int $hard_bounce
 * @property string $created_at
 * @property string $updated_at
 * @property int $user_agent_id
 * @property-read \App\Models\Deploy $deploy
 * @property-read \App\Models\Email $email
 * @property-read \App\Models\UserAgentString $userAgent
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailCampaignStatistic whereCampaignId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailCampaignStatistic whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailCampaignStatistic whereEmailId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailCampaignStatistic whereEspFirstClickDatetime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailCampaignStatistic whereEspFirstOpenDatetime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailCampaignStatistic whereEspLastClickDatetime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailCampaignStatistic whereEspLastOpenDatetime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailCampaignStatistic whereEspTotalClicks($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailCampaignStatistic whereEspTotalOpens($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailCampaignStatistic whereHardBounce($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailCampaignStatistic whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailCampaignStatistic whereLastStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailCampaignStatistic whereMtFirstClickDatetime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailCampaignStatistic whereMtFirstOpenDatetime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailCampaignStatistic whereMtLastClickDatetime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailCampaignStatistic whereMtLastOpenDatetime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailCampaignStatistic whereMtTotalClicks($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailCampaignStatistic whereMtTotalOpens($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailCampaignStatistic whereTrkFirstClickDatetime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailCampaignStatistic whereTrkFirstOpenDatetime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailCampaignStatistic whereTrkLastClickDatetime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailCampaignStatistic whereTrkLastOpenDatetime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailCampaignStatistic whereTrkTotalClicks($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailCampaignStatistic whereTrkTotalOpens($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailCampaignStatistic whereUnsubscribed($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailCampaignStatistic whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailCampaignStatistic whereUserAgentId($value)
 * @mixin \Eloquent
 */
class EmailCampaignStatistic extends Model {
  protected $guarded = ['id'];
  protected $connection = "reporting_data";
  public $timestamps = false;

  public function email() {
    return $this->belongsTo('App\Models\Email');
  }

  public function deploy() {
    return $this->belongsTo('App\Models\Deploy');
  }

  public function userAgent() {
    return $this->belongsTo('App\Models\UserAgentString');
  }
}
