<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Jobs\DataProcessingJob;

class ImportMt1Entity extends Command
{
    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mt1Import {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Keep entities in MT2 up to date';

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
    public function handle() {
        $lookback = 0;
        $jobName = $this->getJobName($this->argument('type'));
        $job = new DataProcessingJob($jobName, str_random(16), $lookback);
        $this->dispatch($job);
    }

    protected function getJobName($type) {
        switch ($type) {
            case "offer":
                return 'ImportMt1Offers';

            case "advertiser":
                return "ImportMt1Advertisers";

            default:
                throw new \Exception('Unsupported entity type: ' . $type);
        }
    }
}
