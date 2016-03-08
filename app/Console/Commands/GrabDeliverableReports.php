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
    protected $signature = 'reports:downloadDeliverables {espName} {lookBack?}';

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
        $this->lookBack = $this->argument('lookBack') ? $this->argument('lookBack') : env('LOOKBACK',5);

        $date = Carbon::now()->subDay($this->lookBack)->toDateString();

        $espName = $this->argument('espName');

        $espAccounts = $this->espRepo->getAccountsByESPName($espName);

        foreach ($espAccounts as $account){
            $espLogLine = "{$account->name}::{$account->account_name}";
            $this->info($espLogLine);

            $this->dispatch(new RetrieveDeliverableReports($account->name, $account->id, $date, str_random(16)));
        }
    }
}
