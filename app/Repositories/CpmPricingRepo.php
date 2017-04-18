<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Repositories;

use DB;
use Log;
use App\Models\OfferPayout;
use App\Models\CpmOfferSchedule;
use App\Models\CpmDeployOverride;

class CpmPricingRepo {
    const CPM_PAYOUT_TYPE_ID = 1;

    protected $payout;
    protected $schedule;
    protected $override;

    public function __construct ( OfferPayout $payout , CpmOfferSchedule $schedule , CpmDeployOverride $override ) {
        $this->payout = $payout;
        $this->schedule = $schedule;
        $this->override = $override;
    }

    public function getPricings ( $search ) {
        $db = config('database.connections.mysql.database');

        return DB::select("
            SELECT
                *
            FROM
                (
                SELECT
                    cos.id as `id` ,
                    o.name ,
                    op.offer_id ,
                    '' as `deploy_id` ,
                    op.amount ,
                    cos.start_date ,
                    cos.end_date
                FROM
                    {$db}.offer_payouts op
                    INNER JOIN {$db}.cpm_offer_schedules cos ON( op.offer_id = cos.cake_offer_id )
                    LEFT JOIN {$db}.offers o ON( op.offer_id = o.id )
                WHERE
                    op.`offer_payout_type_id` = 1
                
                UNION

                SELECT
                    cdo.id as `id` ,
                    o.name ,
                    COALESCE( d.offer_id , '' ) ,
                    cdo.deploy_id as `deploy_id` ,
                    cdo.amount ,
                    cdo.start_date ,
                    cdo.end_date
                FROM
                    {$db}.cpm_deploy_overrides cdo
                    LEFT JOIN {$db}.deploys d ON( cdo.deploy_id = d.id )
                    LEFT JOIN {$db}.offers o ON( d.offer_id = o.id )
                ) as cpm_pricing
            ORDER BY
                start_date desc;
        ");
    }

    public function createPricing ( $record ) {
        try {
            $newPayout = new OfferPayout();
            $newPayout->offer_id = $record[ 'offer_id' ];
            $newPayout->offer_payout_type_id = self::CPM_PAYOUT_TYPE_ID;
            $newPayout->amount = $record[ 'amount' ];
            $newPayout->save();

            $newSchedule = new CpmOfferSchedule();
            $newSchedule->cake_offer_id = $record[ 'offer_id' ];
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

    public function createOverride ( $record ) {
        try {
            $newOverride = new CpmDeployOverride();
            $newOverride->deploy_id = $record[ 'deploy_id' ];
            $newOverride->amount = $record[ 'amount' ];
            $newOverride->start_date = $record[ 'startDate' ];
            $newOverride->end_date = $record[ 'endDate' ];
            $newOverride->save();
        } catch ( \Exception $e ) {
            Log::error( $e );

            return false;
        }

        return true;
    }
    
    public function updateOverride ( $id , $record ) {
        try {
            $this->override->where( 'id' , $id )
                ->update( [
                    'amount' => $record[ 'amount' ] ,
                    'start_date' => $record[ 'startDate' ] ,
                    'end_date' => $record[ 'endDate' ]
                ] );
        } catch ( \Exception $e ) {
            Log::error( $e ) ;

            return false;
        }

        return true;
    }
}
