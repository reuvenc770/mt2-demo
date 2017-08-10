<?php

namespace App\Jobs;

use Illuminate\Support\Facades\DB;
use App\Facades\JobTracking;
use App\Models\JobEntry;
use App\Models\MaroReport;
use App\Models\OrphanEmail;
use App\Exceptions\JobException;
use App\Factories\APIFactory;
use App\Factories\ServiceFactory;

class UpdateMissingMaroCampaignsJob extends MonitoredJob
{

    const JOB_NAME = 'UpdateMissingMaroCampaignsJob';
    const ESP_NAME = 'Maro';
    const CHUNK_AMOUNT = 50;

    protected $espAccountId;
    protected $tracking;
    protected $findOrphanCampaigns;
    protected $reportService;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $espAccountId , $tracking , $runtimeThreshold, $findOrphanCampaigns = false)
    {
        $this->espAccountId = $espAccountId;
        $this->tracking = $tracking;
        $this->findOrphanCampaigns = $findOrphanCampaigns;

        parent::__construct(self::JOB_NAME,$runtimeThreshold,$tracking);
        JobTracking::startEspJob( self::JOB_NAME , self::ESP_NAME , $this->espAccountId , $this->tracking );
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handleJob()
    {
        $orphans = \App::make(\App\Models\OrphanEmail::class);
        $this->reportService = APIFactory::createAPIReportService( self::ESP_NAME , $this->espAccountId );

        $missingCampaigns = [];
        if ( $this->findOrphanCampaigns ) {
            $orphanCollection = $this->getOrphanCampaigns( $orphans );

            if ( $orphanCollection->count() > 0 ) {
                $missingCampaigns = $orphanCollection->pluck( 'esp_internal_id' )->toArray();
            }
        } else {
            $missingCampaigns = $this->reportService->getMissingCampaigns( $this->espAccountId );
        }

        $newCampaignData = [];
        $runCount = 0;
        foreach ( $missingCampaigns as $campaignId ) {
            $newData = $this->reportService->retrieveSingleCampaignStats( $campaignId );

            if ( count( $newData ) > 0 ) {
                $this->deleteOldCampaign( $newData[ 'id' ] , $newData[ 'name' ] );

                $newCampaignData []= $this->cleanseData( $newData );

                $runCount++;

                $bufferCount = count( $newCampaignData );
                $missingCount = count( $missingCampaigns );

                if ( $bufferCount >= self::CHUNK_AMOUNT || $runCount === $missingCount ) {
                    $this->reportService->insertApiRawStats( $newCampaignData );
                    $newCampaignData = [];
                }
            }
        }
    }

    public function cleanseData ( $newData ) {
        $newData[ 'esp_account_id' ] = $this->espAccountId;
        $newData[ 'campaign_id' ] = $newData[ 'id' ];
        $newData[ 'open' ] = $newData[ 'opened' ];
        $newData[ 'click' ] = $newData[ 'clicked' ];
        $newData[ 'bounce' ] = $newData[ 'bounced' ];
        $newData[ 'unsubscribes' ] = $newData[ 'unsubscribed' ];
        $newData[ 'complaints' ] = $newData[ 'complaint' ];

        return $newData;
    }

    protected function getOrphanCampaigns ( $orphans ) {
        return $orphans
            ->select( DB::raw( 'esp_internal_id , count( * ) as count' ) )
            ->where( [
                [ 'missing_email_record' , 0 ] ,
                [ 'esp_account_id' , $this->espAccountId ]
            ] )
            ->groupBy( 'esp_internal_id' )
            ->orderBy( 'count' , 'desc' )
            ->get();
    }

    protected function deleteOldCampaign ( $internalId , $campaignName) {
        $rawRecord = $this->reportService->getRawByExternalId( $internalId );

        if ( is_null( $rawRecord ) ) {
            return;
        }

        if ( $rawRecord->esp_account_id != $this->espAccountId ) {
            $rawRecord->delete();

            $standardReport = ServiceFactory::createStandardReportService();

            $standardReport->deleteCampaign( $campaignName );
        }
    }
}
