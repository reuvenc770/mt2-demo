<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Jobs\FirstPartyReprocessingJob;
use App\Repositories\EspWorkflowRepo;

class ReprocessFirstPartyActions extends Command
{
    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feedRecords:reprocessFirstParty {lookback?}';
    const DEFAULT_LOOKBACK = 1;
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Re-scrub first-party responders against the updated suppression lists';

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
    public function handle(EspWorkflowRepo $repo) {
        $lookBack = $this->argument('lookback') ?: self::DEFAULT_LOOKBACK;

        $activeWorkflows = $repo->getActiveWorkflows();

        foreach ($activeWorkflows as $workflow) {
            $job = new FirstPartyReprocessingJob($workflow, $lookback, str_random(16));
            $this->dispatch($job);
        }
        
    }
}
