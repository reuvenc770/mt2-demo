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
    protected $deploys;
    protected $username;
    protected $signature = 'deploys:sendToOps {deploysCommaList} {username}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Grabs Deploys for the Comma delimited deploy list and places them on the Ops FTP';

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
        $this->deploys = $this->argument("deploysCommaList");
        $this->username = $this->argument("username");
        $job = (new SendOpsDeploys($this->deploys, str_random(16),$this->username));
        $this->dispatch($job);
    }
}
