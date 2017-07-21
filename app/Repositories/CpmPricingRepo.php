<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Repositories;

use Log;
use App\Models\CpmOfferSchedule;
use App\Models\MtOfferCakeOfferMapping;
use App\Services\OfferPayoutService;

class CpmPricingRepo {
    const CPM_PAYOUT_TYPE_ID = 1;

    protected $payoutService;
    protected $schedule;
    protected $offerMapper;

    public function __construct (
        OfferPayoutService $payoutService ,
        CpmOfferSchedule $schedule ,
        MtOfferCakeOfferMapping $offerMapper
    ) {
        $this->payoutService = $payoutService;
        $this->schedule = $schedule;
        $this->offerMapper = $offerMapper;
    }

    public function getModel () {
        return $this->schedule
                    ->select(
                        'offers.name' ,
                        'cpm_offer_schedules.offer_id' ,
                        'cpm_offer_schedules.cake_offer_id' ,
                        'cpm_offer_schedules.amount' ,
                        'cpm_offer_schedules.start_date' ,
                        'cpm_offer_schedules.end_date'
                    )
                    ->leftJoin( 'offers' , 'offers.id' , '=' , 'cpm_offer_schedules.offer_id' );
    }

    public function createPricing ( $record ) {
        try {
            $offerMapResult = $this->offerMapper->where( 'offer_id' , $record[ 'offer_id' ] )->first();
        
            if ( is_null( $offerMapResult ) ) {
                throw new \Exception( 'Cake Offer ID cannot be found. Please contact tech support.' );
            }

            $this->payoutService->setPayout(
                $record[ 'offer_id' ] ,
                self::CPM_PAYOUT_TYPE_ID ,
                $record[ 'amount' ]
            );

            $this->updateOrCreate( [
                'cake_offer_id' => $offerMapResult->cake_offer_id ,
                'offer_id' => $record[ 'offer_id' ] ,
                'offer_payout_type_id' => self::CPM_PAYOUT_TYPE_ID ,
                'amount' => $record[ 'amount' ] ,
                'start_date' => $record[ 'startDate' ] ,
                'end_date' => $record[ 'endDate' ] ,
            ] );
        } catch ( \Exception $e ) {
            Log::error( $e );

            return [ "status" => false , "message" => $e->getMessage() ];
        }

        return [ "status" => true ];
    }

    public function updatePricing ( $id , $record ) {
        try {
            $this->payoutService->setPayout(
                $record[ 'offer_id' ] ,
                self::CPM_PAYOUT_TYPE_ID ,
                $record[ 'amount' ]
            );

            $this->updateOrCreate( [
                'id' => $id ,
                'amount' => $record[ 'amount' ] ,
                'start_date' => $record[ 'startDate' ] ,
                'end_date' => $record[ 'endDate' ] ,
            ] );
        } catch ( \Exception $e ) {
            Log::error( $e );

            return [ "status" =>false , "message" => $e->getMessage() ];
        }

        return [ "status" => true ];
    }

    protected function updateOrCreate ( $record ) {
        return $this->schedule->updateOrCreate(
            [ "id" => isset( $record[ 'id' ] ) ? $record[ 'id' ] : null ] ,
            $record            
        );
    }
}
