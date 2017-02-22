<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\MtOfferCakeOfferMapping
 *
 * @property int $offer_id
 * @property int $cake_offer_id
 * @method static \Illuminate\Database\Query\Builder|\App\Models\MtOfferCakeOfferMapping whereCakeOfferId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\MtOfferCakeOfferMapping whereOfferId($value)
 * @mixin \Eloquent
 */
class MtOfferCakeOfferMapping extends Model {
    
    protected $guarded = [];
    public $timestamps = false;
}
