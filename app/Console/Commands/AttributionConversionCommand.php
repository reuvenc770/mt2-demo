<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Carbon\Carbon;

use App\Jobs\AttributionConversionJob;

class AttributionConversionCommand extends Command
{
    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attribution:conversion {--P|processMode=realtime : This option sets how the job should behave. If in "save" mode, the conversions will simply be saved. If in "realtime" mode, feed report will be updated and client report aggregator will run. If in "rerun" mode, all report aggregators will run. } {--D|dateMode=daysBack : This option sets the date constraints for pull conversions. If in "range" mode, you can specify the exact date range. If in "daysBack" mode, you can specify how many days back from the current date to reprocess. If in "month" mode, you can specify the current month, last month, 2 months ago or full run(the past 3 months). } {--d|daysBack=1 : Nubmer of days back to pull conversions for. } {--s|startDate= : The starting date for pulling conversions. } {--e|endDate= : The end date for pulling conversions. } {--m|monthType=current : When in month mode, you can specify either "current" , "last" , "twoMonthsAgo" , or "full". } {--R|recordType=all : When pulling conversions, you can specify either "cpc", "cpa", or "all". }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will fire off jobs to grab conversion data from CAKE. If specified, you can update reports in real time. ';


    protected $processMode;

    protected $dateMode;
    protected $daysBack;
    protected $dateRange;
    protected $monthType;

    protected $recordType;
    
    protected $outputHeaders = [ 'Process Mode' , 'Record Type' , 'Date Mode' ];
    protected $outputRows = [ [] ];

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

        $this->dispatch( new AttributionConversionJob(
            $this->processMode ,
            $this->recordType ,
            str_random( 16 ) ,
            $this->dateRange
        ) );
    }

    protected function processOptions () {
        $this->setProcessMode();
        $this->setRecordType();
        $this->setDateMode();
        $this->table( $this->outputHeaders , $this->outputRows );
    }

    protected function setProcessMode () {
        if ( $this->isInvalidProcessMode() ) {
            $this->error( "Invalid process mode: " . $this->option( 'processMode' ) );
            exit();
        }

        $this->processMode = $this->option( 'processMode' ); 
        $this->outputRows[ 0 ] []= $this->processMode;
    }

    protected function isInvalidProcessMode () {
        return !in_array( $this->option( 'processMode' ) , [
            AttributionConversionJob::PROCESS_MODE_SAVE ,
            AttributionConversionJob::PROCESS_MODE_REALTIME ,
            AttributionConversionJob::PROCESS_MODE_RERUN
        ] );
    }

    protected function setRecordType () {
        if ( $this->isInvalidRecordType() ) {
            $this->error( "Invalid record type: " . $this->option( 'recordType' ) );
            exit();
        }

        $this->recordType = $this->option( 'recordType' );
        $this->outputRows[ 0 ] []= $this->recordType;
    }

    protected function isInvalidRecordType () {
        return !in_array( $this->option( 'recordType' ) , [
            'cpc' ,
            'cpa' ,
            'all'
        ] );
    }

    protected function setDateMode () {
        if ( $this->isInvalidDateMode() ) {
            $this->error( "Invalid date mode: " . $this->option( 'dateMode' ) );
            exit();
        }

        $this->dateMode = $this->option( 'dateMode' );
        $this->outputRows[ 0 ] []= $this->dateMode;


        if ( $this->dateMode === 'range' && $this->isInvalidDateRange() ) {
            $this->error( "Invalid date range: " . $this->option( 'startDate' ) . " - " . $this->option( 'endDate' ) );
            exit();
        }

        $this->dateRange = [
            'start' => Carbon::parse( $this->option( 'startDate' ) )->toDateString() ,
            'end' => Carbon::parse( $this->option( 'endDate' ) )->toDateString()
        ];

        if ( $this->dateMode === 'month' && $this->isInvalidMonthType() ) {
            $this->error( "Invalid month type: " . $this->option( 'monthType' ) );
            exit();
        }

        $this->monthType = $this->option( 'monthType' );

        if ( $this->dateMode === 'month' ) {
            $this->outputHeaders []= "Month Type";
            $this->outputRows[ 0 ] []= $this->monthType;

            switch ( $this->monthType ) {
                case 'current' :
                    $this->dateRange = [
                        'start' => Carbon::today()->startOfMonth()->toDateString() ,
                        'end' => Carbon::today()->endOfMonth()->toDateString()
                    ];
                break;

                case 'last' :
                    $this->dateRange = [
                        'start' => Carbon::today()->subMonth()->startOfMonth()->toDateString() ,
                        'end' => Carbon::today()->subMonth()->endOfMonth()->toDateString()
                    ];
                break;

                case 'twoMonthsAgo' :
                    $this->dateRange = [
                        'start' => Carbon::today()->subMonth( 2 )->startOfMonth()->toDateString() ,
                        'end' => Carbon::today()->subMonth( 2 )->endOfMonth()->toDateString()
                    ];
                break;

                case 'full' :
                    $this->dateRange = [
                        'start' => Carbon::today()->subMonth( 2 )->startOfMonth()->toDateString() ,
                        'end' => Carbon::today()->endOfMonth()->toDateString()
                    ];
                break;
            }
        }

        if ( $this->dateMode === 'daysBack' && ( !is_numeric( $this->option( 'daysBack' ) ) || 0 > $this->option( 'daysBack' ) ) ) {
            $this->error( "Invalid days back: " . $this->option( 'daysBack' ) . ". Must be a number above 0." );
            exit();
        }

        $this->daysBack = $this->option( 'daysBack' );

        if ( $this->dateMode === 'daysBack' ) {
            $this->outputHeaders []= "Days Back";
            $this->outputRows[ 0 ] []= $this->daysBack;

            $this->dateRange = [
                'start' => Carbon::today()->subDay( $this->daysBack )->toDateString() ,
                'end' => Carbon::today()->toDateString()
            ];
        }

        $this->outputHeaders []= "Start Date";
        $this->outputRows[ 0 ] []= Carbon::parse( $this->dateRange[ 'start' ] )->toFormattedDateString();

        $this->outputHeaders []= "End Date";
        $this->outputRows[ 0 ] []= Carbon::parse( $this->dateRange[ 'end' ] )->toFormattedDateString();
    }

    protected function isInvalidDateMode () {
        return !in_array( $this->option( 'dateMode' ) , [
            'daysBack' ,
            'range' ,
            'month'
        ] );
    }

    protected function isInvalidDateRange () {
        if ( is_null( $this->option( 'startDate' ) ) || is_null( $this->option( 'endDate' ) ) ) {
            return true;
        }

        try {
            $startDate = Carbon::parse( $this->option( 'startDate' ) );
            $endDate = Carbon::parse( $this->option( 'endDate' ) );

            if ( $startDate->diffInDays( $endDate , false ) < 0 ) {
                return true;
            }
        } catch ( \Exception $e ) {
            return true;
        }

        return false;
    }

    protected function isInvalidMonthType () {
        return !in_array( $this->option( 'monthType' ) , [
            'current' ,
            'last' ,
            'twoMonthsAgo' ,
            'full'
        ] );
    }
}
