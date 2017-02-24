<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserAgentString
 *
 * @property int $id
 * @property string $user_agent_string
 * @property string $browser
 * @property string $device
 * @property bool $is_mobile
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserAgentString whereBrowser($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserAgentString whereDevice($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserAgentString whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserAgentString whereIsMobile($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserAgentString whereUserAgentString($value)
 * @mixin \Eloquent
 */
class UserAgentString extends Model {
    protected $guarded = ['id'];
    public $timestamps = false;
}