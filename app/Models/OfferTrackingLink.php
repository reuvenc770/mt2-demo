<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OfferTrackingLink
 *
 * @property int $id
 * @property int $offer_id
 * @property int $link_num
 * @property int $link_id
 * @property string $url
 * @property string $approved_by
 * @property string $date_approved
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\Link $link
 * @property-read \App\Models\Offer $offer
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OfferTrackingLink whereApprovedBy($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OfferTrackingLink whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OfferTrackingLink whereDateApproved($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OfferTrackingLink whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OfferTrackingLink whereLinkId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OfferTrackingLink whereLinkNum($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OfferTrackingLink whereOfferId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OfferTrackingLink whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OfferTrackingLink whereUrl($value)
 * @mixin \Eloquent
 */
class OfferTrackingLink extends Model {
    protected $guarded = ['id'];

    public function offer() {
        return $this->belongsTo('App\Models\Offer');
    }

    public function link() { // ?
        return $this->belongsTo('App\Models\Link');
    }
}
