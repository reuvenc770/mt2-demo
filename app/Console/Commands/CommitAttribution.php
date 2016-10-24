<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\CommitAttributionJob;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Cache;

class CommitAttribution extends Command
{
    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attribution:commit {type} {--modelId=none} {--userEmail=none} {--feedId=none}';
    private $keyName = 'AttributionJob';

    const VALID_TYPES = ['daily', 'model', 'feedInvalidation'];
    const MOD_BASE = 5;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run attribution based on the current model.';

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
        $type = $this->argument('type');
        $modelId = $this->option('modelId');
        $userEmail = $this->option('userEmail');
        $feedId = $this->option('feedId');

        if (!in_array($type, self::VALID_TYPES)) {
            throw new \Exception("Attribution run type $type invalid.");
        }

        if ('feedInvalidation' === $type && 'none' === $feedId) {
            throw new \Exception("Feed invalidation job requires feed id");
        }

        if ('model' === $type && 'none' === $modelId) {
            throw new \Exception("Model attribution job requires model id");
        }

        $argObj = [
            'type' => $type,
            'model' => $modelId,
            'userEmail' => $userEmail,
            'feedId' => $feedId
        ];

        Cache::forget($this->keyName); // Forget any current instance
        Cache::forever($this->keyName, 0); // Reset to 0

        for ($remainder = 0; $remainder < self::MOD_BASE; $remainder++) {
            $job = new CommitAttributionJob($argObj, $remainder, str_random(16));
            $this->dispatch($job);
        }
        
    }
}
