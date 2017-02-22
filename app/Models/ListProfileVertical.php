<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ListProfileVertical
 *
 * @property int $list_profile_id
 * @property int $cake_vertical_id
 * @property-read \App\Models\ListProfile $listProfile
 * @property-read \App\Models\CakeVertical $vertical
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileVertical whereCakeVerticalId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileVertical whereListProfileId($value)
 * @mixin \Eloquent
 */
class ListProfileVertical extends Model
{
    protected $guarded = [''];
    public $timestamps = false;
    protected $connection = 'list_profile';

    public function vertical() {
        return $this->belongsTo('App\Models\CakeVertical', 'cake_vertical_id');
    }

    public function listProfile() {
        return $this->belongsTo('App\Models\ListProfile');
    }
}