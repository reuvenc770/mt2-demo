<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Jobs\AttributionValidationJob;
use App\Repositories\EtlPickupRepo;

class AttributionFeasibilityValidation extends Command
{
    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attribution:validate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Validates whether the current attribution setup is feasible updates any obvious omissions.';

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
        $startPoint = $pickupRepo->getLastInsertedForName('AttributionValidation');

        $job = new AttributionValidationJob($startPoint, str_random(16));
        $this->dispatch($job);
    }
}
