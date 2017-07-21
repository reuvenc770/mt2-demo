<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CpmOfferSchedule extends Model
{
    protected $guarded = [];

    public function mt1Offer () {
        return $this->belongsTo( 'App\Models\Offer' , 'offer_id' );
    }

    public function type () {
        return $this->hasOne( 'App\Models\OfferPayoutType' , 'id' , 'offer_payout_type_id' );
    }
}
