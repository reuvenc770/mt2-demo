<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Facades\JobTracking;
use App\Models\JobEntry;
use App\Exceptions\JobException;
use App\Factories\APIFactory;

class UpdateMissingMaroCampaignsJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    const JOB_NAME = 'UpdateMissingMaroCampaignsJob';
    const ESP_NAME = 'Maro';
    const CHUNK_AMOUNT = 50;

    protected $espAccountId;
    protected $tracking;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $espAccountId , $tracking )
    {
        $this->espAccountId = $espAccountId;
        $this->tracking = $tracking;

        JobTracking::startEspJob( self::JOB_NAME , self::ESP_NAME , $this->espAccountId , $this->tracking );
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \Log::info( "Running UpdateMissingMaroCampaignsJob for $this->espAccountId...." );

        JobTracking::changeJobState( JobEntry::RUNNING , $this->tracking);

        try {
            $reportService = APIFactory::createAPIReportService( self::ESP_NAME , $this->espAccountId );

            $missingCampaigns = $reportService->getMissingCampaigns( $this->espAccountId );

            $newCampaignData = [];
            $count = 0;
            foreach ( $missingCampaigns as $campaignId ) {
                $newData = $reportService->retrieveSingleCampaignStats( $campaignId ); 

                if ( count( $newData ) > 0 ) {
                    \Log::info( 'Have data for campaign ' . $campaignId );
                    \Log::info( $newData );

                    $newCampaignData []= $this->cleanseData( $newData );
                    
                    $count++;

                    if ( count( $newCampaignData ) >= self::CHUNK_AMOUNT ) {
                        $reportService->insertApiRawStats( $newCampaignData );
                        $newCampaignData = [];

                        \Log::info( 'Saved campaign stats.' );
                        \Log::info( 'Have ' . ( count( $missingCampaigns ) - $count ) . ' left...' );
                        \Log::info( '' );
                    }
                }
            }

            \Log::info( "Successfully ran UpdateMissingMaroCampaignsJob for $this->espAccountId...." );
            \Log::info( '' );

            JobTracking::changeJobState( JobEntry::SUCCESS , $this->tracking , $count ); #count( $newCampaignData ) );
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
        $newData[ 'complaints' ] = $newData[ 'unsubscribes' ];

        return $newData;
    }

    public function failed()
    {
        JobTracking::changeJobState(JobEntry::FAILED,$this->tracking);
    }
}
