<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ListProfileOffer
 *
 * @property int $list_profile_id
 * @property int $offer_id
 * @property-read \App\Models\ListProfile $listProfile
 * @property-read \App\Models\Offer $offer
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileOffer whereListProfileId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ListProfileOffer whereOfferId($value)
 * @mixin \Eloquent
 */
class ListProfileOfferSuppression extends Model
{
    protected $guarded = [];
    public $timestamps = false;
    protected $connection = 'list_profile';
    protected $table = 'list_profile_offer_suppression';

    public function offer() {
        return $this->belongsTo('App\Models\Offer');
    }

    public function listProfile() {
        return $this->belongsTo('App\Models\ListProfile');
    }
}