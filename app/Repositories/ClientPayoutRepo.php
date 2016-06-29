<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

use App\Models\ClientPayout;
use App\Models\ClientPayoutType;

class ClientPayoutRepo {
    protected $payout;
    protected $payoutType;

    public __construct ( ClientPayout $payout , ClientPayoutType $payoutType ) {
        $this->payout = $payout;
        $this->payoutType = $payoutType;
    }

    public function setPayout ( $clientId , $type , $amount ) {
        #create or update payout for given client.
    }

    public function createPayoutType ( $name ) {
        #create new payout type
    }
}
