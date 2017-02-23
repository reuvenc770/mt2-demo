<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\DataConsistencyValidationJob;
use Illuminate\Foundation\Bus\DispatchesJobs;

class DataConsistencyValidation extends Command
{
    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dataValidation {source} {type}';

    const VALID_TYPES = ['exists', 'value'];
    const VALID_SOURCES = ['emails', 'emailFeedInstances', 'emailFeedAssignments'];

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks for and resolves various data inconsistencies.';

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
        $source = $this->argument('source');
        $type = $this->argument('type');

        if (!in_array($source, self::VALID_SOURCES)) {
            throw new \Exception("Data consistency check job run source invalid: $source");
        }

        if (!in_array($type, self::VALID_TYPES)) {
            throw new \Exception("Data consistency check type invalid: $type");
        }

        $job = new DataConsistencyValidationJob($source, $type, str_random(16));
        $this->dispatch($job);
    }
}
