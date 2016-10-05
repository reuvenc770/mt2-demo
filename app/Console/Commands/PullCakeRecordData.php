<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;

class PullCakeRecordData extends Command
{
    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'listprofile:getRecordAgentData';
    private $name = 'CakeDeviceData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pull device, device type, and carrier information from Cake';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $job = new DataProcessingJob($this->name, str_random(16));
        $this->dispatch($job);
    }
}
