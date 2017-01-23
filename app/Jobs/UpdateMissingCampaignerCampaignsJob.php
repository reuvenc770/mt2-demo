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
use Carbon\Carbon;

class UpdateMissingCampaignerCampaignsJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    const JOB_NAME = 'UpdateMissingCampaignerCampaignsJob';
    const ESP_NAME = 'Campaigner';
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
        JobTracking::changeJobState( JobEntry::RUNNING , $this->tracking);

        try {
            $reportService = APIFactory::createAPIReportService( self::ESP_NAME , $this->espAccountId );
        
            $lookBack = Carbon::now()->subDays( 31 )->startOfDay()->toDateTimeString();

            $missingCampaigns = $reportService->getMissingCampaigns( $this->espAccountId , $lookBack );

            $campaignResponse = $reportService->retrieveApiStatsFromCampaigns( $missingCampaigns , $lookBack );

            $rowCount = 0;
            $rowCount += $reportService->insertApiRawStats( $campaignResponse );

            JobTracking::changeJobState( JobEntry::SUCCESS , $this->tracking , $rowCount );
        } catch ( \Exception $e ) {
            if ( $e->getCode() === JobException::ERROR ) {
                $this->release( 10 );
            } else {
                throw new JobException(self::JOB_NAME . "::{$this->espAccountId} failed with {$e->getMessage()}  {$e->getLine()}" . PHP_EOL);
            }
        }
    }

    public function failed()
    {
        JobTracking::changeJobState(JobEntry::FAILED,$this->tracking);
    }
}
