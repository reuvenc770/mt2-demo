<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\EmailFeedInstance
 *
 * @property int $id
 * @property int $email_id
 * @property int $feed_id
 * @property string $subscribe_datetime
 * @property string $unsubscribe_datetime
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
 * @property string $source_url
 * @property string $ip
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\Email $email
 * @property-read \App\Models\Feed $feed
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailFeedInstance whereAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailFeedInstance whereAddress2($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailFeedInstance whereCaptureDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailFeedInstance whereCity($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailFeedInstance whereCountry($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailFeedInstance whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailFeedInstance whereDob($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailFeedInstance whereEmailId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailFeedInstance whereFeedId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailFeedInstance whereFirstName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailFeedInstance whereGender($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailFeedInstance whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailFeedInstance whereIp($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailFeedInstance whereLastName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailFeedInstance whereMobilePhone($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailFeedInstance wherePhone($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailFeedInstance whereSourceUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailFeedInstance whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailFeedInstance whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailFeedInstance whereSubscribeDatetime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailFeedInstance whereUnsubscribeDatetime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailFeedInstance whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailFeedInstance whereWorkPhone($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailFeedInstance whereZip($value)
 * @mixin \Eloquent
 */
class EmailFeedInstance extends Model {

    protected $guarded = ['id'];
    
    public function email() {
        return $this->belongsTo('App\Models\Email');
    }

    public function feed() {
        return $this->belongsTo('App\Models\Feed');
    }
}
