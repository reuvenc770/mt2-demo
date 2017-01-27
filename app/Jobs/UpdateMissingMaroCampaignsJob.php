<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

use App\Facades\JobTracking;
use App\Models\JobEntry;
use App\Models\MaroReport;
use App\Models\OrphanEmail;
use App\Exceptions\JobException;
use App\Factories\APIFactory;
use App\Factories\ServiceFactory;

class UpdateMissingMaroCampaignsJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

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
    public function __construct( $espAccountId , $tracking , $findOrphanCampaigns = false )
    {
        $this->espAccountId = $espAccountId;
        $this->tracking = $tracking;
        $this->findOrphanCampaigns = $findOrphanCampaigns;

        JobTracking::startEspJob( self::JOB_NAME , self::ESP_NAME , $this->espAccountId , $this->tracking );
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle( OrphanEmail $orphans )
    {
        JobTracking::changeJobState( JobEntry::RUNNING , $this->tracking);

        try {
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

            JobTracking::changeJobState( JobEntry::SUCCESS , $this->tracking , $runCount );
        } catch ( \Exception $e ) {
            throw new JobException(self::JOB_NAME . "::{$this->espAccountId} failed with {$e->getMessage()}  {$e->getLine()}" . PHP_EOL);
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

    public function failed()
    {
        JobTracking::changeJobState(JobEntry::FAILED,$this->tracking);
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
