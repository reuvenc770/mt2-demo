<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OfferCreativeMap
 *
 * @property int $id
 * @property int $offer_id
 * @property int $creative_id
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OfferCreativeMap whereCreativeId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OfferCreativeMap whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OfferCreativeMap whereOfferId($value)
 * @mixin \Eloquent
 */
class OfferCreativeMap extends Model {
  
    protected $guarded = ['id'];
    protected $connection = "reporting_data";
    public $timestamps = false;

}