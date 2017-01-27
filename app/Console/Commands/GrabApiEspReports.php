<?php

namespace App\Console\Commands;


use App\Repositories\EspApiAccountRepo;
use Carbon\Carbon;
use App\Jobs\RetrieveApiReports;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
class GrabApiEspReports extends Command
{
    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:downloadApi {espName} {--D|daysBack=} {--Q|queueName=default} {--E|espAccountId=} {--L|apiLimit=}}'; #{--queueName=orphanage}
    protected $espRepo;

    protected $espName;
    protected $daysBack;
    protected $queueName;
    protected $espAccountId;
    protected $apiLimit;

    protected $startDate;
    protected $currentAccountId;

    protected $accountsFired = [];

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';


    public function __construct(EspApiAccountRepo $espRepo)
    {
        parent::__construct();
        $this->espRepo = $espRepo;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->processOptions();

        if( $this->isSingleAccountGrab() ) {
            $this->fireJob();
        } else{
            $espAccounts = $this->espRepo->getAccountsByESPName( $this->espName );

            foreach ($espAccounts as $account) {
                $this->setCurrentAccountId( $account->id );

                $this->fireJob();
            }
        }

        $this->displayTable();
    }

    public function processOptions () {
        $this->espName = $this->argument( 'espName' );
        $this->daysBack = $this->option( 'daysBack' );
        $this->queueName = $this->option( 'queueName' );
        $this->espAccountId = $this->option( 'espAccountId' );
        $this->apiLimit = $this->option( 'apiLimit' );

        if ( is_null( $this->daysBack ) ) {
            $this->daysBack = config( 'jobs.defaultLookback' );
        }

        $this->startDate = Carbon::now()->subDay( $this->daysBack )->startOfDay()->toDateString();

        if ( !is_null( $this->espAccountId ) ) {
            $this->setCurrentAccountId( $this->espAccountId );
        }
    }

    public function isSingleAccountGrab () {
        return !is_null( $this->espAccountId );
    }

    public function fireJob () {

        if ( $this->limitCallSize() ) {
            $job = new RetrieveApiReports( $this->espName , $this->currentAccountId , $this->startDate , str_random( 16 ) , $this->apiLimit );
        } else {
            $job = new RetrieveApiReports( $this->espName , $this->currentAccountId , $this->startDate , str_random( 16 ) );
        }

        $job->onQueue( $this->queueName );

        $this->dispatch($job);
    }

    public function limitCallSize () {
        return !is_null( $this->apiLimit );
    }

    public function setCurrentAccountId ( $id ) {
        $this->currentAccountId = $id;

        $this->accountsFired []= $id;
    }

    public function displayTable () {
        $this->table( [ 'Option' , 'Value' ] , [
            [ 'option' => 'ESP Name' , 'value' => $this->espName ] ,
            [ 'option' => 'ESP Account ID' , 'value' => implode( ',' , $this->accountsFired ) ] ,
            [ 'option' => 'Start Date' , 'value' => $this->startDate ] ,
            [ 'option' => 'Queue Name' , 'value' => $this->queueName ] ,
            [ 'option' => 'API Limit' , 'value' => $this->apiLimit ?: 'None' ]
        ] );
    }
}
