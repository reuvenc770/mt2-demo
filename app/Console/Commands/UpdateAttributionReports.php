<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Foundation\Bus\DispatchesJobs;

use App\Jobs\AttributionAggregatorJob;

use Carbon\Carbon;

class UpdateAttributionReports extends Command
{
    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attribution:updateReports {--R|reportName= : The name of the report to update.} {--d|daysBack=1 : Number of days back to run the report for. If no value is given, it will run for the current day. } {--M|modelId=0 : Model ID to process instead of live tables.} {--Q|queueName=default : The queue to throw the job onto. }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';


    protected $reportName;
    protected $dateRange;
    protected $modelId;
    protected $queueName;

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

        $job = ( new AttributionAggregatorJob(
            $this->reportName ,
            str_random( 16 ) ,
            $this->dateRange ,
            $this->modelId
        ) )->onQueue( $this->queueName );

        $this->dispatch( $job );
    }

    protected function processOptions () {
        if ( $this->option( 'reportName' ) ) {
            $this->reportName = $this->option( 'reportName' );
        } else {
            throw new \Exception( "Missing report name. Please provide one." );
        }

        if (
            $this->option( 'daysBack' )
            && is_numeric( $this->option( 'daysBack' ) )
            && $this->option( 'daysBack' ) > 1
        ) {
            $this->dateRange = [
                "start" => Carbon::today()->subDays( $this->option( 'daysBack' ) ) ,
                "end" => Carbon::today()->toDateString()
            ];
        }

        if (
            $this->option( 'modelId' )
            && is_numeric( $this->option( 'modelId' ) )
            && $this->option( 'modelId' ) > 0
        ) {
            $this->modelId = $this->option( 'modelId' );
        }

        if ( $this->option( 'queueName' ) ) {
            $this->queueName = $this->option( 'queueName' );
        } else {
            $this->queueName = "default";
        }
    }
}
