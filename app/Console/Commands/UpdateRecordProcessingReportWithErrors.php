<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\DataProcessingJob;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Repositories\EtlPickupRepo;

class UpdateRecordProcessingReportWithErrors extends Command
{
    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feedRecords:updateReportWithErrors {--runtime-threshold=10m}';
    const JOB_NAME = 'UpdateFeedProcessingErrors';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the record processing report table with errors caught in prior stages of record processing.';

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
        $lookback = $pickupRepo->getLastInsertedForName(self::JOB_NAME);
        $runtimeThreshold = $this->option('runtime-threshold');
        $job = new DataProcessingJob(self::JOB_NAME, str_random(16), $lookback, $runtimeThreshold);
        $this->dispatch($job);
    }
}
