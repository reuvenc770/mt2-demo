<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FirstPartyRecordData
 *
 * @property int $email_id
 * @property int $feed_id
 * @property bool $is_deliverable
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
 * @property string $capture_date
 * @property string $subscribe_date
 * @property int $last_action_offer_id
 * @property string $last_action_date
 * @property mixed $other_fields
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FirstPartyRecordData whereAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FirstPartyRecordData whereAddress2($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FirstPartyRecordData whereCaptureDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FirstPartyRecordData whereCarrier($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FirstPartyRecordData whereCity($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FirstPartyRecordData whereCountry($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FirstPartyRecordData whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FirstPartyRecordData whereDeviceName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FirstPartyRecordData whereDeviceType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FirstPartyRecordData whereDob($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FirstPartyRecordData whereEmailId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FirstPartyRecordData whereFeedId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FirstPartyRecordData whereFirstName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FirstPartyRecordData whereGender($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FirstPartyRecordData whereIp($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FirstPartyRecordData whereIsDeliverable($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FirstPartyRecordData whereLastActionDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FirstPartyRecordData whereLastActionOfferId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FirstPartyRecordData whereLastName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FirstPartyRecordData whereOtherFields($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FirstPartyRecordData wherePhone($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FirstPartyRecordData whereSourceUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FirstPartyRecordData whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FirstPartyRecordData whereSubscribeDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FirstPartyRecordData whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FirstPartyRecordData whereZip($value)
 * @mixin \Eloquent
 */
class FirstPartyRecordData extends Model
{
    protected $table = 'first_party_record_data';
    protected $guarded = [];
}
