<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Models\Cake;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Cake\CakeConversion
 *
 * @property int $id
 * @property string $email_id
 * @property string $s1
 * @property string $s2
 * @property string $s3
 * @property string $s4
 * @property string $s5
 * @property string $click_id
 * @property string $conversion_date
 * @property string $conversion_id
 * @property bool $is_click_conversion
 * @property int $request_session_id
 * @property int $affiliate_id
 * @property int $offer_id
 * @property int $advertiser_id
 * @property int $campaign_id
 * @property int $creative_id
 * @property float $received_raw
 * @property float $received_usa
 * @property float $paid_raw
 * @property float $paid_usa
 * @property bool $paid_currency_id
 * @property bool $received_currency_id
 * @property float $conversion_rate
 * @property string $ip
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Cake\CakeConversion whereAdvertiserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Cake\CakeConversion whereAffiliateId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Cake\CakeConversion whereCampaignId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Cake\CakeConversion whereClickId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Cake\CakeConversion whereConversionDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Cake\CakeConversion whereConversionId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Cake\CakeConversion whereConversionRate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Cake\CakeConversion whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Cake\CakeConversion whereCreativeId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Cake\CakeConversion whereEmailId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Cake\CakeConversion whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Cake\CakeConversion whereIp($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Cake\CakeConversion whereIsClickConversion($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Cake\CakeConversion whereOfferId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Cake\CakeConversion wherePaidCurrencyId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Cake\CakeConversion wherePaidRaw($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Cake\CakeConversion wherePaidUsa($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Cake\CakeConversion whereReceivedCurrencyId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Cake\CakeConversion whereReceivedRaw($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Cake\CakeConversion whereReceivedUsa($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Cake\CakeConversion whereRequestSessionId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Cake\CakeConversion whereS1($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Cake\CakeConversion whereS2($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Cake\CakeConversion whereS3($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Cake\CakeConversion whereS4($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Cake\CakeConversion whereS5($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Cake\CakeConversion whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CakeConversion extends Model
{
    protected $connection = "reporting_data";
}
