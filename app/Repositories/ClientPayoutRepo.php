<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories;

use App\Models\ClientPayout;
use App\Models\ClientPayoutType;

class ClientPayoutRepo {
    protected $payout;
    protected $payoutType;

    public function __construct ( ClientPayout $payout , ClientPayoutType $payoutType ) {
        $this->payout = $payout;
        $this->payoutType = $payoutType;
    }

    public function setPayout ( $clientId , $typeId , $amount ) {
        #create or update payout for given client.
        $this->payout->updateOrCreate(['client_id' => $clientId],
        [
            'client_id' => $clientId,
            'client_payout_type_id' => $typeId,
            'amount' => $amount
        ]);
    }

    public function getPayout ( $clientId ) {
        return $this->payout
                    ->select('client_payout_type_id', 'amount')
                    ->where('client_id', $clientId)
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
