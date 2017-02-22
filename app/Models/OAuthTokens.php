<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OAuthTokens
 *
 * @property int $id
 * @property string $access_token
 * @property string $access_secret
 * @property int $esp_account_id
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OAuthTokens whereAccessSecret($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OAuthTokens whereAccessToken($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OAuthTokens whereEspAccountId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OAuthTokens whereId($value)
 * @mixin \Eloquent
 */
class OAuthTokens extends Model
{
    protected $guarded = ['id'];
    public $timestamps = false;
}
