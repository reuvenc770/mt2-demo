<?php
/**
 * @author Adam Chin <achin@zetaglobal.net>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\RawFeedEmailFailed
 *
 * @property int $id
 * @property string $url
 * @property string $ip
 * @property string $email
 * @property int $feed_id
 * @property mixed $errors
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RawFeedEmailFailed whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RawFeedEmailFailed whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RawFeedEmailFailed whereErrors($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RawFeedEmailFailed whereFeedId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RawFeedEmailFailed whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RawFeedEmailFailed whereIp($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RawFeedEmailFailed whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RawFeedEmailFailed whereUrl($value)
 * @mixin \Eloquent
 */
class RawFeedEmailFailed extends Model
{
    protected $guarded = [ 'id' ];
    protected $table = 'raw_feed_email_failed';
}
