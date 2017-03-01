<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Foundation\Bus\DispatchesJobs;

use App\Jobs\ProcessNewActionsJob;

class ProcessNewActionsCommand extends Command
{
    use DispatchesJobs;

    protected $dateRange;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'newActions:process {--d|daysBack= : How far back to look for actions} {--s|startDate= : Beginning of date range to look for actions.} {--e|endDate= : End of date range to look for actions.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will kick off a job to pull most recent actions from the list profile flat table and updates the required tables.';

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

        echo json_encode( $this->dateRange ) . "\n";

        $this->dispatch( new ProcessNewActionsJob(
            $this->dateRange ,
            str_random( 16 )
        ) );
    }

    protected function processOptions () {
        if ( !is_null( $this->option( 'daysBack' ) ) ) {
            $this->dateRange = [
                'start' => Carbon::now()->subDays( $this->option( 'daysBack' ) )->toDateString() ,
                'end' => Carbon::now()->toDateString()
            ];
            
        } else {
            if ( $this->isInvalidDateRange( $this->option( 'startDate' ) , $this->option( 'endDate' ) ) ) {
                $this->error( "Invalid date range: " . json_encode( [ 'startDate' => $this->option( 'startDate' ) , 'endDate' => $this->option( 'endDate' ) ] ) );
                exit();
            }

            $this->dateRange = [
                'start' => Carbon::parse( $this->option( 'startDate' ) )->toDateString() ,
                'end' => Carbon::parse( $this->option( 'endDate' ) )->toDateString()
            ];
        }
    }

    protected function isInvalidDateRange ( $startDate , $endDate ) {
        if ( is_null( $startDate ) || is_null( $endDate ) ) {
            return true;
        }

        try {
            #Check if end date is before the start date
            if ( Carbon::parse( $startDate )->diffInDays( Carbon::parse( $endDate ) , false ) < 0 ) {
                return true;
            }
        } catch ( \Exception $e ) {
            return true;
        }

        return false;
    }
}
