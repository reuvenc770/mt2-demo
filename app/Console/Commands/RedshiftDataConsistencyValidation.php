<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\RedshiftDataValidationJob;
use Illuminate\Foundation\Bus\DispatchesJobs;

class RedshiftDataConsistencyValidation extends Command
{
    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'listprofile:validateRedshift {lookback}';

    protected $entities = ['EmailDomain', 'EmailFeedAssignment', 'Email', 'Feed', 'ListProfileFlatTable', 
    'RecordData', 'SuppressionGlobalOrange', 'DomainGroup', 'Client', 'SuppressionListSuppression'];

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks for and resolves data discrepancies between CMP and Redshift list profile store. Lookback is in days.';

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
        $lookback = $this->argument('lookback');

        foreach($this->entities as $entity) {
            $job = new RedshiftDataValidationJob($entity, $lookback, str_random(16));
            $this->dispatch($job);
        }

    }
}
