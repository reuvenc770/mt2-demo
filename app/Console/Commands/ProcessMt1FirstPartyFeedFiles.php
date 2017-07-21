<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;

class ProcessMt1FirstPartyFeedFiles extends Command
{
    use DispatchesJobs;

    const JOB_NAMESPACE = '\\App\\Jobs\\';
    const JOB_NAME_UNEMPLOYMENT = 'ProcessMt1UnemploymentFeedFilesJob';
    const JOB_NAME_MEDICAID = 'ProcessMt1MedicaidFeedFilesJob';
    const JOB_NAME_SECTION8 = 'ProcessMt1Section8FeedFilesJob';
    const JOB_NAME_SIMPLEJOBS = 'ProcessMt1SimpleJobsFeedFilesJob';
    const JOB_NAME_FOODSTAMPS = 'ProcessMt1FoodstampsFeedFilesJob';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feedRecords:processMt1FirstPartyFiles { --feedname= : Name of First Party Feed to process files for. } { --runtime-threshold=15m : Threshold for monitoring. }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Processes new first party realtime feed files from MT1 posts server.';

    protected $jobName = '';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->processOptions();

        $job = \App::make( $this->jobName , [
            str_random( 16 ) ,
            $this->option( 'runtime-threshold' )
        ] );

        $this->dispatch($job->onQueue( 'rawFeedProcessing' ));
    }

    protected function processOptions () {
        $map = [
            'unemployment' => self::JOB_NAME_UNEMPLOYMENT ,
            'medicaid' => self::JOB_NAME_MEDICAID , 
            'section8' => self::JOB_NAME_SECTION8 ,
            'simplejobs' => self::JOB_NAME_SIMPLEJOBS ,
            'foodstamps' => self::JOB_NAME_FOODSTAMPS
        ];

        if ( !in_array( $this->option( 'feedname' ) , array_keys( $map ) ) ) {
            $this->error( json_encode( $this->option( 'feedname' ) ) . " is an invalid first party feed name. Please use one of the folowing: " . json_encode( array_keys( $map ) ) );
        }

        $this->jobName = self::JOB_NAMESPACE . $map[ $this->option( 'feedname' ) ];
    }
}
