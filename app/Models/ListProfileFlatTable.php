<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ListProfileFlatTable
 *
 * @property int $email_id
 * @property int $deploy_id
 * @property int $esp_account_id
 * @property string $date
 * @property string $email_address
 * @property string $lower_case_md5
 * @property string $upper_case_md5
 * @property int $email_domain_id
 * @property int $email_domain_group_id
 * @property int $offer_id
 * @property int $cake_vertical_id
 * @property bool $has_esp_open
 * @property bool $has_cs_open
 * @property bool $has_open
 * @property bool $has_esp_click
 * @property bool $has_cs_click
 * @property bool $has_tracking_click
 * @property bool $has_click
 * @property bool $has_tracking_conversion
 * @property bool $has_conversion
 * @property bool $deliveries
 * @property int $opens
 * @property int $clicks
 * @property int $conversions
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileFlatTable whereCakeVerticalId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileFlatTable whereClicks($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileFlatTable whereConversions($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileFlatTable whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileFlatTable whereDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileFlatTable whereDeliveries($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileFlatTable whereDeployId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileFlatTable whereEmailAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileFlatTable whereEmailDomainGroupId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileFlatTable whereEmailDomainId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileFlatTable whereEmailId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileFlatTable whereEspAccountId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileFlatTable whereHasClick($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileFlatTable whereHasConversion($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileFlatTable whereHasCsClick($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileFlatTable whereHasCsOpen($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileFlatTable whereHasEspClick($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileFlatTable whereHasEspOpen($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileFlatTable whereHasOpen($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileFlatTable whereHasTrackingClick($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileFlatTable whereHasTrackingConversion($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileFlatTable whereLowerCaseMd5($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileFlatTable whereOfferId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileFlatTable whereOpens($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileFlatTable whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileFlatTable whereUpperCaseMd5($value)
 * @mixin \Eloquent
 */
class ListProfileFlatTable extends Model {
    protected $guarded = [];
    public $timstamps = false;
    protected $connection = 'list_profile';
    protected $table = 'list_profile_flat_table';
}
