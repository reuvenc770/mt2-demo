<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Log;
use App\Repositories\EspApiAccountRepo; 
use Carbon\Carbon;
use App\Jobs\RetrieveDeliverableReports;
use Illuminate\Foundation\Bus\DispatchesJobs;

class GrabDeliverableReports extends Command
{
    use DispatchesJobs;

    protected $espRepo;
    protected $lookBack;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:downloadDeliverables {espName} {lookBack?} {queueName?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct( EspApiAccountRepo $espRepo )
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
        $this->lookBack = $this->argument('lookBack') ? $this->argument('lookBack') : config('jobs.defaultLookback');
        $queue = (string) $this->argument('queueName') ? $this->argument('queueName') : "default";
        $date = Carbon::now()->subDay($this->lookBack)->toDateString();

        $espName = $this->argument('espName');
        $processState = null;

        if ( preg_match( '/:/' , $espName ) ) {
            $espParts = explode( ':' , $espName );
            $espName = $espParts[ 0 ];
            $processState = [ 'pipe' => $espParts[ 1 ] ];
        }

        $espAccounts = $this->espRepo->getAccountsByESPName($espName);

        foreach ($espAccounts as $account){
            if ( $account->enable_stats ) {
                $espLogLine = "{$account->name}::{$account->account_name}";
                $this->info($espLogLine);

                $job = (new RetrieveDeliverableReports(
                    $account->name ,
                    $account->id ,
                    $date ,
                    str_random(16) ,
                    $processState ,
                    $queue ) )->onQueue( $queue );

                $this->dispatch($job);
            } else {
                $this->info( 'Stats Not Enabled for ' . $account->account_name );
            }
        }
    }
}
