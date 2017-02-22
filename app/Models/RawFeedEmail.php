<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\RawFeedEmail
 *
 * @property int $id
 * @property int $feed_id
 * @property string $email_address
 * @property string $source_url
 * @property string $capture_date
 * @property string $ip
 * @property string $first_name
 * @property string $last_name
 * @property string $address
 * @property string $address2
 * @property string $city
 * @property string $state
 * @property string $zip
 * @property string $country
 * @property string $gender
 * @property string $phone
 * @property string $dob
 * @property mixed $other_fields
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RawFeedEmail whereAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RawFeedEmail whereAddress2($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RawFeedEmail whereCaptureDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RawFeedEmail whereCity($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RawFeedEmail whereCountry($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RawFeedEmail whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RawFeedEmail whereDob($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RawFeedEmail whereEmailAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RawFeedEmail whereFeedId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RawFeedEmail whereFirstName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RawFeedEmail whereGender($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RawFeedEmail whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RawFeedEmail whereIp($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RawFeedEmail whereLastName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RawFeedEmail whereOtherFields($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RawFeedEmail wherePhone($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RawFeedEmail whereSourceUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RawFeedEmail whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RawFeedEmail whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RawFeedEmail whereZip($value)
 * @mixin \Eloquent
 */
class RawFeedEmail extends Model
{
    protected $guarded = [ 'id' ];
}
