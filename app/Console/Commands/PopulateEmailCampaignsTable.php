<?php

namespace App\Console\Commands;


use App\Repositories\EtlPickupRepo;
use Carbon\Carbon;
use App\Jobs\PopulateEmailCampaignStats;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;

class PopulateEmailCampaignsTable extends Command {
    use DispatchesJobs;

    protected $signature = 'reports:populateStats';
    protected $lookBack;
    protected $description = 'PopulateEmailCampaignStats';


    public function __construct(EtlPickupRepo $etlPickupRepo) {
        parent::__construct();
        $this->etlPickupRepo = $etlPickupRepo;
    }

    public function handle() {
        $lookBack = $this->etlPickupRepo->getLastInsertedForName($this->description);
        $logLine = "Starting {$this->description} collection at row $lookBack" . PHP_EOL;
        $this->info($logLine);
        $this->dispatch(new PopulateEmailCampaignStats($this->etlPickupRepo, $lookBack, str_random(16)));
    }
}