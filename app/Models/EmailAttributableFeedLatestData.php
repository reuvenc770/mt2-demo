<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\EmailAttributableFeedLatestData
 *
 * @property int $email_id
 * @property int $feed_id
 * @property string $subscribe_date
 * @property string $capture_date
 * @property string $attribution_status
 * @property string $first_name
 * @property string $last_name
 * @property string $address
 * @property string $address2
 * @property string $city
 * @property string $state
 * @property string $zip
 * @property string $country
 * @property string $gender
 * @property string $ip
 * @property string $phone
 * @property string $source_url
 * @property string $dob
 * @property string $device_type
 * @property string $device_name
 * @property string $carrier
 * @property mixed $other_fields
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailAttributableFeedLatestData whereAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailAttributableFeedLatestData whereAddress2($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailAttributableFeedLatestData whereAttributionStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailAttributableFeedLatestData whereCaptureDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailAttributableFeedLatestData whereCarrier($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailAttributableFeedLatestData whereCity($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailAttributableFeedLatestData whereCountry($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailAttributableFeedLatestData whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailAttributableFeedLatestData whereDeviceName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailAttributableFeedLatestData whereDeviceType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailAttributableFeedLatestData whereDob($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailAttributableFeedLatestData whereEmailId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailAttributableFeedLatestData whereFeedId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailAttributableFeedLatestData whereFirstName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailAttributableFeedLatestData whereGender($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailAttributableFeedLatestData whereIp($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailAttributableFeedLatestData whereLastName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailAttributableFeedLatestData whereOtherFields($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailAttributableFeedLatestData wherePhone($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailAttributableFeedLatestData whereSourceUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailAttributableFeedLatestData whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailAttributableFeedLatestData whereSubscribeDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailAttributableFeedLatestData whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailAttributableFeedLatestData whereZip($value)
 * @mixin \Eloquent
 */
class EmailAttributableFeedLatestData extends Model
{
    protected $table = 'email_attributable_feed_latest_data';
    protected $guarded = [];

    const ATTRIBUTED = "ATTR";
    const PASSED_DUE_TO_RESPONDER = "POR";
    const PASSED_DUE_TO_ATTRIBUTION = "POA";
    const LOST_ATTRIBUTION = "MOA";
}
