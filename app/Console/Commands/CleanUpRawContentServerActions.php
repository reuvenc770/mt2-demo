<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\DataProcessingJob;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Repositories\EtlPickupRepo;

class CleanUpRawContentServerActions extends Command
{
    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'listprofile:contentServerRawStats {--runtime-threshold=default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process content server raw stats';
    private $jobName = 'ProcessContentServerRawStats';

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
    public function handle(EtlPickupRepo $pickupRepo) {
        $lookback = $pickupRepo->getLastInsertedForName($this->jobName);
        $job = new DataProcessingJob($this->jobName, str_random(16), $lookback, $this->option('runtime-threshold'));
        $this->dispatch($job);
    }
}
