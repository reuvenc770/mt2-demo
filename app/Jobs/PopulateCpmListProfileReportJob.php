<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Jobs;

use App\Jobs\MonitoredJob;

class PopulateCpmListProfileReportJob extends MonitoredJob
{
    protected $jobName = 'PopulateCpmListProfileReportJob';
    protected $tracking;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $tracking , $runtimeThreshold="1h" )
    {
        $this->jobName .= '-' . $tracking;
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
    public function handleJob ()
    {
        $repo = \App::make( \App\Repositories\CpmListProfileReportRepo::class );

        $pricings = $repo->getCurrentMonthsPricings();
        foreach ( $pricings as $currentPricing ) {
            $counts = $repo->getCountsForDeploy( $currentPricing->deploy_id );

            $records = [];
            $currentCakeOfferId = 0;
            foreach ( $counts as $currentCount ) {
                $records []= [
                    'feed_id' => $currentCount->feed_id ,
                    'delivered' => $currentCount->scount ,
                    'cake_offer_id' => $currentPricing->cake_offer_id ,
                    'payout' => $currentPricing->amount ,
                    'rev' => $currentCount->scount / 1000 * $currentPricing->amount
                ];

                $currentCakeOfferId = $currentPricing->cake_offer_id;
            }

            $repo->clearForCakeOfferId( $currentCakeOfferId );
            $repo->saveReport( $records );
        }
    }
}
