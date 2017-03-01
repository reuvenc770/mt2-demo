<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ModelTraits\Mailable;

/**
 * App\Models\Offer
 *
 * @property int $id
 * @property string $name
 * @property bool $is_approved
 * @property string $status
 * @property int $advertiser_id
 * @property bool $offer_payout_type_id
 * @property string $unsub_link
 * @property string $exclude_days
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\Advertiser $advertiser
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CakeOffer[] $cakeOffers
 * @property-read \App\Models\OfferPayoutType $payoutType
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\OfferTrackingLink[] $trackingLinks
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Offer whereAdvertiserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Offer whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Offer whereExcludeDays($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Offer whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Offer whereIsApproved($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Offer whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Offer whereOfferPayoutTypeId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Offer whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Offer whereUnsubLink($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Offer whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Offer extends Model {

    use Mailable;
  
    protected $guarded = [];

    public function advertiser() {
        return $this->belongsTo('App\Models\Advertiser');
    }

    public function trackingLinks() {
        return $this->hasMany('App\Models\OfferTrackingLink');
    }

    public function payoutType() {
        return $this->belongsTo('App\Models\OfferPayoutType', 'offer_payout_type_id');
    }

    public function cakeOffers() {
        return $this->belongsToMany('App\Models\CakeOffer', 'mt_offer_cake_offer_mappings', 'offer_id', 'cake_offer_id');
    }
}