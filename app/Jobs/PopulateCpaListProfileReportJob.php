<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Jobs;

use App\Jobs\MonitoredJob;

class PopulateCpaListProfileReportJob extends MonitoredJob
{
    protected $jobName = 'PopulateCpaListProfileReportJob';
    protected $dateRange;
    protected $tracking;

    protected $reportRepo;
    protected $convRepo;
    protected $deployRepo;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $dateRange , $tracking , $runtimeThreshold="1h" )
    {
        $this->dateRange = $dateRange;
        $this->jobName .= '-' . json_encode( $dateRange ) . '-' . $tracking;
        $this->tracking = $tracking;

        parent::__construct(
            $this->jobName ,
            $runtimeThreshold ,
            $tracking
        );
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handleJob()
    {
        $this->reportRepo = \App::make( \App\Repositories\CpaListProfileReportRepo::class );
        $this->convRepo = \App::make( \App\Repositories\TrackingRepo::class , [ \App::make( \App\Models\CakeAction::class ) ] );
        $this->deployRepo = \App::make( \App\Repositories\DeployRepo::class );

        $deployOfferMap = $this->convRepo->getPaidDeployAndCakeOffers( $this->dateRange );
        $rowsImpacted = 0;
        foreach ( $deployOfferMap as $currentMap ) { #keys: deploy_id , cake_offer_id , offer_id
            $records = [];
            $breakdown = $this->reportRepo->getFeedRecordDistribution( $currentMap->deploy_id );

            #will be distributed later
            $unattributedConversions = $this->convRepo->getPaidConversionsByCakeOffer( $this->dateRange , $currentMap->cake_offer_id )->where( 'email_id' , 0 )->get();
            $unattributedConversionCount = $this->convRepo->getPaidConversionsByCakeOffer( $this->dateRange , $currentMap->cake_offer_id )
                                                    ->where( 'email_id' , 0 )
                                                    ->count();

            foreach ( $breakdown as $currentFeed ) { #process each feed for the given deploy/cake_offer_id
                $revenueResult = $this->convRepo->getPaidConversionsByCakeOfferAndFeed( $this->dateRange , $currentMap->cake_offer_id , $currentFeed->feed_id )
                                                ->where( 'cake_actions.email_id' , '<>' , 0 )
                                                ->select( \DB::raw( 'SUM( revenue ) as totalAttrRev' ) )
                                                ->first();

                if ( is_null( $revenueResult ) ) {
                    $revenue = 0;
                } else {
                    $revenue = $revenueResult->totalAttrRev;
                }

                $feedConversionCount = $this->convRepo->getPaidConversionsByCakeOfferAndFeed( $this->dateRange , $currentMap->cake_offer_id , $currentFeed->feed_id )
                                    ->where( 'cake_actions.email_id' , '<>' , 0 )
                                    ->count();

                $currentFeedRecord = [
                    'feed_id' => $currentFeed->feed_id ,
                    'cake_offer_id' => $currentMap->cake_offer_id ,
                    'offer_id' => $currentMap->offer_id ,
                    'deploy_id' => $currentMap->deploy_id ,
                    'conversions' => $feedConversionCount ,
                    'rev' => (float)$revenue
                ];

                #figure out how many unattributed conversions to assign to this feed
                $makeGoodCount = $unattributedConversionCount * $currentFeed->feed_perc; 
                $this->doMakeGood( $currentFeedRecord , $unattributedConversions , $makeGoodCount );

                #if we're at the last feed and there are unattributed conversions left, add to last feed's revenue
                if ( end( $breakdown ) === $currentFeed && $unattributedConversions->count() > 0 ) {
                    $makeGoodCount = $unattributedConversions->count(); 
                    $this->doMakeGood( $currentFeedRecord , $unattributedConversions , $makeGoodCount );
                }

                $records []= $this->reportRepo->toSqlFormat( $currentFeedRecord );
            }

            $this->reportRepo->massInsert( $records );

            $rowsImpacted += count( $records );
        }

        return $rowsImpacted;
    }

    protected function doMakeGood ( &$feed , &$unattributedConversions , &$makeGoodCount ) {
        while ( $unattributedConversions->count() > 0 && $makeGoodCount > 0 ) {
            $feed[ 'rev' ] += (float)$unattributedConversions->shift()->revenue;
            $feed[ 'conversions' ]++;

            $makeGoodCount--;
        }
    }
}
