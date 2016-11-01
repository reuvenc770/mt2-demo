<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferPayout extends Model
{
    protected $fillable = ['offer_id', 'offer_payout_type_id', 'amount'];

    public function type () {
        return $this->hasOne( 'App\Models\OfferPayoutType' );
    }
}
