<?php

namespace App\Console\Commands;


use App\Repositories\EtlPickupRepo;
use Carbon\Carbon;
use App\Jobs\PopulateEmailCampaignStats;
use App\Jobs\PullCakeDeliverableStats;
use App\Jobs\UpdateContentServerStats;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;

class PopulateEmailCampaignsTable extends Command {
    use DispatchesJobs;

    protected $signature = 'reports:populateStats';
    protected $lookBack;
    protected $description = 'PopulateEmailCampaignStats';
    protected $trackingSource = 'Cake';


    public function __construct(EtlPickupRepo $etlPickupRepo) {
        parent::__construct();
        $this->etlPickupRepo = $etlPickupRepo;
    }

    public function handle() {
        $lookBack = $this->etlPickupRepo->getLastInsertedForName($this->description);
        $logLine = "Starting {$this->description} collection at row $lookBack" . PHP_EOL;
        $this->info($logLine);

        $date = Carbon::now()->subDay($this->lookBack)->toDateString();
        $this->dispatch(new PopulateEmailCampaignStats($this->etlPickupRepo, $lookBack, str_random(16)));
        $this->dispatch(new PullCakeDeliverableStats($this->trackingSource, $date, str_random(16)));
        $this->dispatch(new UpdateContentServerStats($lookBack, str_random(16)));
    }
}