<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OfferFromMap
 *
 * @property int $id
 * @property int $offer_id
 * @property int $from_id
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OfferFromMap whereFromId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OfferFromMap whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OfferFromMap whereOfferId($value)
 * @mixin \Eloquent
 */
class OfferFromMap extends Model {
  
    protected $guarded = ['id'];
    protected $connection = "reporting_data";
    public $timestamps = false;

}