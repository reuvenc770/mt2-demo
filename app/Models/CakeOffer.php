<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CakeOffer
 *
 * @property int $id
 * @property string $name
 * @property int $vertical_id
 * @property int $cake_advertiser_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CakeOffer[] $mtOffers
 * @property-read \App\Models\CakeVertical $vertical
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CakeOffer whereCakeAdvertiserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CakeOffer whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CakeOffer whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CakeOffer whereVerticalId($value)
 * @mixin \Eloquent
 */
class CakeOffer extends Model {
    public $timestamps = false;
    protected $guarded = [];

    public function vertical() {
        return $this->belongsTo('App\Models\CakeVertical');
    }

    public function mtOffers() {
        return $this->belongsToMany('App\Models\CakeOffer', 'mt_offer_cake_offer_mappings', 'cake_offer_id', 'offer_id');
    }
}
