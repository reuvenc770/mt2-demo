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
    protected $signature = 'newActions:process {--h|hoursBack= : How far back to look for actions} {--s|startDateTime= : Beginning of datetime range to look for actions.} {--e|endDateTime= : End of datetime range to look for actions.}';

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

        echo json_encode( $this->dateTimeRange ) . "\n";

        $this->dispatch( new ProcessNewActionsJob(
            $this->dateTimeRange ,
            str_random( 16 )
        ) );
    }

    protected function processOptions () {
        if ( !is_null( $this->option( 'hoursBack' ) ) ) {
            $this->dateTimeRange = [
                'start' => Carbon::now()->subHours( $this->option( 'hoursBack' ) )->toDateTimeString() ,
                'end' => Carbon::now()->toDateTimeString()
            ];
            
        } else {
            if ( $this->isInvalidDateRange( $this->option( 'startDateTime' ) , $this->option( 'endDateTime' ) ) ) {
                $this->error( "Invalid datetime range: " . json_encode( [ 'startDateTime' => $this->option( 'startDateTime' ) , 'endDateTime' => $this->option( 'endDateTime' ) ] ) );
                exit();
            }

            $this->dateTimeRange = [
                'start' => Carbon::parse( $this->option( 'startDateTime' ) )->toDateString() ,
                'end' => Carbon::parse( $this->option( 'endDateTime' ) )->toDateString()
            ];
        }
    }

    protected function isInvalidDateRange ( $startDateTime , $endDateTime ) {
        if ( is_null( $startDateTime ) || is_null( $endDateTime ) ) {
            return true;
        }

        try {
            #Check if end date is before the start date
            if ( Carbon::parse( $startDateTime )->diffInHours( Carbon::parse( $endDateTime ) , false ) < 0 ) {
                return true;
            }
        } catch ( \Exception $e ) {
            return true;
        }

        return false;
    }
}
