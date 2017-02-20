<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CakeVertical
 *
 * @property int $id
 * @property string $name
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CakeOffer[] $cakeOffers
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CakeVertical whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CakeVertical whereName($value)
 * @mixin \Eloquent
 */
class CakeVertical extends Model {
    public $timestamps = false;
    protected $guarded = [];

    public function cakeOffers() {
        return $this->hasMany('App\Models\CakeOffer');
    }
}
