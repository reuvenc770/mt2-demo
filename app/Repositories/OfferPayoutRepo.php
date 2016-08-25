<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories;

use App\Models\OfferPayout;
use App\Models\OfferPayoutType;

class OfferPayoutRepo {
    protected $payout;
    protected $payoutType;

    public function __construct ( OfferPayout $payout , OfferPayoutType $payoutType ) {
        $this->payout = $payout;
        $this->payoutType = $payoutType;
    }

    public function setPayout ( $offerId , $typeId , $amount ) {
        #create or update payout for given offer.
        $this->payout->updateOrCreate(['offer_id' => $offerId],
        [
            'offer_id' => $offerId,
            'offer_payout_type_id' => $typeId,
            'amount' => $amount
        ]);
    }

    public function getPayout ( $offerId ) {
        return $this->payout
                    ->select('offer_payout_type_id', 'amount')
                    ->where('offer_id', $offerId)
                    ->first();
    }

    public function createPayoutType ( $name ) {
        $this->payoutType->create(['name' => $name]);
    }

    public function getTypes() {
        return $this->payoutType
                    ->select('id', 'name')
                    ->get();
    }
}
