<?php

namespace App\Models;

use App\Models\Interfaces\IReportMapper;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BlueHornetReport
 *
 * @property int $id
 * @property int $internal_id
 * @property string $message_subject
 * @property string $message_name
 * @property string $date_sent
 * @property string $message_notes
 * @property int $withheld_total
 * @property int $globally_suppressed
 * @property int $suppressed_total
 * @property int $bill_codes
 * @property int $sent_total
 * @property int $sent_total_html
 * @property int $sent_total_plain
 * @property float $sent_rate_total
 * @property float $sent_rate_html
 * @property int $sent_rate_plain
 * @property int $delivered_total
 * @property int $delivered_html
 * @property int $delivered_plain
 * @property float $delivered_rate_total
 * @property float $delivered_rate_html
 * @property float $delivered_rate_plain
 * @property int $bounced_total
 * @property int $bounced_html
 * @property int $bounced_plain
 * @property float $bounced_rate_total
 * @property float $bounced_rate_html
 * @property float $bounced_rate_plain
 * @property int $invalid_total
 * @property float $invalid_rate_total
 * @property bool $has_dynamic_content
 * @property bool $has_delivery_report
 * @property string $link_append_statement
 * @property string $timezone
 * @property string $ftf_forwarded
 * @property string $ftf_signups
 * @property string $ftf_conversion_rate
 * @property int $optout_total
 * @property float $optout_rate_total
 * @property int $opened_total
 * @property int $opened_unique
 * @property float $opened_rate_unique
 * @property float $opened_rate_aps
 * @property int $clicked_total
 * @property int $clicked_unique
 * @property float $clicked_rate_unique
 * @property float $clicked_rate_aps
 * @property string $campaign_name
 * @property int $campaign_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $esp_account_id
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BlueHornetReport whereBillCodes($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BlueHornetReport whereBouncedHtml($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BlueHornetReport whereBouncedPlain($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BlueHornetReport whereBouncedRateHtml($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BlueHornetReport whereBouncedRatePlain($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BlueHornetReport whereBouncedRateTotal($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BlueHornetReport whereBouncedTotal($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BlueHornetReport whereCampaignId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BlueHornetReport whereCampaignName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BlueHornetReport whereClickedRateAps($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BlueHornetReport whereClickedRateUnique($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BlueHornetReport whereClickedTotal($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BlueHornetReport whereClickedUnique($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BlueHornetReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BlueHornetReport whereDateSent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BlueHornetReport whereDeliveredHtml($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BlueHornetReport whereDeliveredPlain($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BlueHornetReport whereDeliveredRateHtml($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BlueHornetReport whereDeliveredRatePlain($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BlueHornetReport whereDeliveredRateTotal($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BlueHornetReport whereDeliveredTotal($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BlueHornetReport whereEspAccountId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BlueHornetReport whereFtfConversionRate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BlueHornetReport whereFtfForwarded($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BlueHornetReport whereFtfSignups($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BlueHornetReport whereGloballySuppressed($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BlueHornetReport whereHasDeliveryReport($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BlueHornetReport whereHasDynamicContent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BlueHornetReport whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BlueHornetReport whereInternalId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BlueHornetReport whereInvalidRateTotal($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BlueHornetReport whereInvalidTotal($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BlueHornetReport whereLinkAppendStatement($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BlueHornetReport whereMessageName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BlueHornetReport whereMessageNotes($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BlueHornetReport whereMessageSubject($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BlueHornetReport whereOpenedRateAps($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BlueHornetReport whereOpenedRateUnique($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BlueHornetReport whereOpenedTotal($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BlueHornetReport whereOpenedUnique($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BlueHornetReport whereOptoutRateTotal($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BlueHornetReport whereOptoutTotal($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BlueHornetReport whereSentRateHtml($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BlueHornetReport whereSentRatePlain($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BlueHornetReport whereSentRateTotal($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BlueHornetReport whereSentTotal($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BlueHornetReport whereSentTotalHtml($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BlueHornetReport whereSentTotalPlain($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BlueHornetReport whereSuppressedTotal($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BlueHornetReport whereTimezone($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BlueHornetReport whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BlueHornetReport whereWithheldTotal($value)
 * @mixin \Eloquent
 */
class BlueHornetReport extends Model implements IReportMapper
{
    protected $guarded = ['id'];
    protected $connection = "reporting_data";

    public function getDateFieldName(){
        return "date_sent";
    }

    public function getSubjectFieldName(){
        return "message_subject";
    }
}
