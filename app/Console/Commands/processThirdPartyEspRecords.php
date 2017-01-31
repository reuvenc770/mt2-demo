<?php

namespace App\Console\Commands;

use App\Jobs\ProcessThirdPartyMaroRecords;
use Illuminate\Console\Command;
use App\Repositories\EspApiAccountRepo;
use Illuminate\Foundation\Bus\DispatchesJobs;

class processThirdPartyEspRecords extends Command
{
    use DispatchesJobs;
    private $espRepo;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:downloadPartyData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'right now its hardcoded since there is only 1 vendor 1 account so far';

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
        $accounts = $this->espRepo->backFuzzySearch("Dima");
        foreach($accounts as $account){
            $job = new ProcessThirdPartyMaroRecords("Dima",$account['id'],str_random(16));
            $this->dispatch($job);
        }
    }
}
