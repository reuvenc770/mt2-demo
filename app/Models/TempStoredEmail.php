<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TempStoredEmail
 *
 * @property int $email_id
 * @property int $feed_id
 * @property string $email_addr
 * @property string $status
 * @property string $first_name
 * @property string $last_name
 * @property string $address
 * @property string $address2
 * @property string $city
 * @property string $state
 * @property string $zip
 * @property string $country
 * @property string $dob
 * @property string $gender
 * @property string $phone
 * @property string $mobile_phone
 * @property string $work_phone
 * @property string $capture_date
 * @property string $ip
 * @property string $source_url
 * @property string $unsubscribe_datetime
 * @property string $last_updated
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TempStoredEmail whereAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TempStoredEmail whereAddress2($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TempStoredEmail whereCaptureDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TempStoredEmail whereCity($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TempStoredEmail whereCountry($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TempStoredEmail whereDob($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TempStoredEmail whereEmailAddr($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TempStoredEmail whereEmailId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TempStoredEmail whereFeedId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TempStoredEmail whereFirstName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TempStoredEmail whereGender($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TempStoredEmail whereIp($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TempStoredEmail whereLastName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TempStoredEmail whereLastUpdated($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TempStoredEmail whereMobilePhone($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TempStoredEmail wherePhone($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TempStoredEmail whereSourceUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TempStoredEmail whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TempStoredEmail whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TempStoredEmail whereUnsubscribeDatetime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TempStoredEmail whereWorkPhone($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TempStoredEmail whereZip($value)
 * @mixin \Eloquent
 */
class TempStoredEmail extends Model {
    
  protected $guarded = ['id'];

}
