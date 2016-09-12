<?php

namespace App\Console\Commands;

use App\Jobs\SendOpsDeploys;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Foundation\Bus\DispatchesJobs;

class SendDeploysToOps extends Command
{
    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $lookBack;
    protected $signature = 'deploys:sendtoops {lookback=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Grabs all Deploys for  X Days and places them on the Ops FTP';

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
        $this->lookBack = $this->argument("lookback");
        $date = Carbon::now()->subDay($this->lookBack)->startOfDay()->toDateString();
        $job = (new SendOpsDeploys($date, str_random(16)));
        $this->dispatch($job);
    }
}
