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
    protected $lookBack = 5;
    protected $trackingSource = 'Cake';


    public function __construct() {
        parent::__construct();
    }

    public function handle() {
        $date = Carbon::now()->subDay($this->lookBack)->toDateString();
        $this->dispatch(new PopulateEmailCampaignStats(str_random(16)));
        $this->dispatch(new PullCakeDeliverableStats($this->trackingSource, $date, str_random(16)));
        $this->dispatch(new UpdateContentServerStats($lookBack, str_random(16)));
    }
}