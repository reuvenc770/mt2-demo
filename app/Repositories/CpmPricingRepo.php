<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Repositories;

use DB;
use Log;
use App\Models\OfferPayout;
use App\Models\CpmOfferSchedule;

class CpmPricingRepo {
    const CPM_PAYOUT_TYPE_ID = 1;

    protected $payout;
    protected $schedule;

    public function __construct ( OfferPayout $payout , CpmOfferSchedule $schedule ) {
        $this->payout = $payout;
        $this->schedule = $schedule;
    }

    public function getModel () {
        return $this->payout
                    ->select(
                        'cpm_offer_schedules.id as id' , 
                        'offers.name as name' ,
                        'offer_payouts.offer_id as offer_id' ,
                        'mt_offer_cake_offer_mappings.cake_offer_id as cake_offer_id' ,
                        'offer_payouts.amount as amount' ,
                        'cpm_offer_schedules.start_date as start_date' ,
                        'cpm_offer_schedules.end_date as end_date'
                    )
                    ->join( 'mt_offer_cake_offer_mappings' , 'offer_payouts.offer_id' , '=' , 'mt_offer_cake_offer_mappings.offer_id' )
                    ->join( 'cpm_offer_schedules' , 'mt_offer_cake_offer_mappings.cake_offer_id' , '=' , 'cpm_offer_schedules.cake_offer_id' )
                    ->leftJoin( 'offers' , 'offer_payouts.offer_id' , '=' , 'offers.id' )
                    ->where( 'offer_payouts.offer_payout_type_id' , '1' );
    }

    public function createPricing ( $record ) {
        try {
            $cakeOfferId = $record[ 'offer_id' ]

            /**
             * Finish check here and fail if no cake id is found.
             */
            if ( is_null( $cakeOfferId ) ) {

            }

            $newPayout = new OfferPayout();
            $newPayout->offer_id = $record[ 'offer_id' ];
            $newPayout->offer_payout_type_id = self::CPM_PAYOUT_TYPE_ID;
            $newPayout->amount = $record[ 'amount' ];
            $newPayout->save();

            $newSchedule = new CpmOfferSchedule();
            $newSchedule->cake_offer_id = $cakeOfferId;
            $newSchedule->start_date = $record[ 'startDate' ];
            $newSchedule->end_date = $record[ 'endDate' ];
            $newSchedule->save();
        } catch ( \Exception $e ) {
            Log::error( $e );

            return false;
        }

        return true;
    }

    public function updatePricing ( $id , $record ) {
        try {
            $this->payout->where( 'offer_id' , $record[ 'offer_id' ] )
                ->update( [
                    'amount' => $record[ 'amount' ]
                ] );

            $this->schedule->where( 'id' , $id )
                ->update( [
                    'start_date' => $record[ 'startDate' ] ,
                    'end_date' => $record[ 'endDate' ]
                ] );
        } catch ( \Exception $e ) {
            Log::error( $e );

            return false;
        }

        return true;
    }
}
