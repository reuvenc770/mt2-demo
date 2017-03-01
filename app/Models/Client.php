<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ModelTraits\ModelCacheControl;

/**
 * App\Models\Client
 *
 * @property int $id
 * @property string $name
 * @property string $address
 * @property string $address2
 * @property string $city
 * @property string $state
 * @property string $zip
 * @property string $email_address
 * @property string $phone
 * @property string $status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Feed[] $feeds
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Client whereAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Client whereAddress2($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Client whereCity($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Client whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Client whereEmailAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Client whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Client whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Client wherePhone($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Client whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Client whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Client whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Client whereZip($value)
 * @mixin \Eloquent
 */
class Client extends Model
{
    use ModelCacheControl;
    protected $guarded = [];

    public function feeds() {
        return $this->hasMany('App\Models\Feed');
    }
}
