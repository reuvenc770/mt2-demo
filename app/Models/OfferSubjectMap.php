<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OfferSubjectMap
 *
 * @property int $id
 * @property int $offer_id
 * @property int $subject_id
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OfferSubjectMap whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OfferSubjectMap whereOfferId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OfferSubjectMap whereSubjectId($value)
 * @mixin \Eloquent
 */
class OfferSubjectMap extends Model {
  
    protected $guarded = ['id'];
    protected $connection = "reporting_data";
    public $timestamps = false;

}