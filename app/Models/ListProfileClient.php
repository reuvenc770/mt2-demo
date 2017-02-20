<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ListProfileClient
 *
 * @property int $list_profile_id
 * @property int $client_id
 * @property-read \App\Models\Client $client
 * @property-read \App\Models\ListProfile $listProfile
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileClient whereClientId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileClient whereListProfileId($value)
 * @mixin \Eloquent
 */
class ListProfileClient extends Model
{
    protected $guarded = [''];
    public $timestamps = false;
    protected $connection = 'list_profile';

    public function client() {
        return $this->belongsTo('App\Models\Client');
    }

    public function listProfile() {
        return $this->belongsTo('App\Models\ListProfile');
    }

}
