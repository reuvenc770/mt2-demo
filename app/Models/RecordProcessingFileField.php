<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\RecordProcessingFileField
 *
 * @property int $feed_id
 * @property bool $email_index
 * @property bool $source_url_index
 * @property bool $capture_date_index
 * @property bool $ip_index
 * @property bool $first_name_index
 * @property bool $last_name_index
 * @property bool $address_index
 * @property bool $address2_index
 * @property bool $city_index
 * @property bool $state_index
 * @property bool $zip_index
 * @property bool $country_index
 * @property bool $gender_index
 * @property bool $phone_index
 * @property bool $dob_index
 * @property mixed $other_field_index
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RecordProcessingFileField whereAddress2Index($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RecordProcessingFileField whereAddressIndex($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RecordProcessingFileField whereCaptureDateIndex($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RecordProcessingFileField whereCityIndex($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RecordProcessingFileField whereCountryIndex($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RecordProcessingFileField whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RecordProcessingFileField whereDobIndex($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RecordProcessingFileField whereEmailIndex($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RecordProcessingFileField whereFeedId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RecordProcessingFileField whereFirstNameIndex($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RecordProcessingFileField whereGenderIndex($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RecordProcessingFileField whereIpIndex($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RecordProcessingFileField whereLastNameIndex($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RecordProcessingFileField whereOtherFieldIndex($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RecordProcessingFileField wherePhoneIndex($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RecordProcessingFileField whereSourceUrlIndex($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RecordProcessingFileField whereStateIndex($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RecordProcessingFileField whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RecordProcessingFileField whereZipIndex($value)
 * @mixin \Eloquent
 */
class RecordProcessingFileField extends Model
{
    protected $guarded = [ '' ];

    protected $primaryKey = 'feed_id';
}
