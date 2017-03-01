<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OfferPayout
 *
 * @property int $offer_id
 * @property int $offer_payout_type_id
 * @property float $amount
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\OfferPayoutType $type
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OfferPayout whereAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OfferPayout whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OfferPayout whereOfferId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OfferPayout whereOfferPayoutTypeId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OfferPayout whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class OfferPayout extends Model
{
    protected $fillable = ['offer_id', 'offer_payout_type_id', 'amount'];

    public function type () {
        return $this->hasOne( 'App\Models\OfferPayoutType' );
    }
}
