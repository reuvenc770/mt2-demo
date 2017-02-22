<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ContentServerStatsRaw
 *
 * @property int $id
 * @property int $eid
 * @property int $link_id
 * @property int $sub_aff_id
 * @property bool $action_id
 * @property string $user_agent
 * @property string $referrer
 * @property string $query_string
 * @property string $action_datetime
 * @property string $ip
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ContentServerStatsRaw whereActionDatetime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ContentServerStatsRaw whereActionId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ContentServerStatsRaw whereEid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ContentServerStatsRaw whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ContentServerStatsRaw whereIp($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ContentServerStatsRaw whereLinkId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ContentServerStatsRaw whereQueryString($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ContentServerStatsRaw whereReferrer($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ContentServerStatsRaw whereSubAffId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ContentServerStatsRaw whereUserAgent($value)
 * @mixin \Eloquent
 */
class ContentServerStatsRaw extends Model
{
    public $timestamps = false;    
}
