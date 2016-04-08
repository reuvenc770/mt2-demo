<?php

namespace App\Console\Commands;


use App\Repositories\EtlPickupRepo;
use Carbon\Carbon;
use App\Jobs\PopulateEmailCampaignStats;
use App\Jobs\PullCakeDeliverableStats;
use App\Jobs\UpdateContentServerStats;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Console\Traits\PreventOverlapping;

class PopulateEmailCampaignsTable extends Command {
    use DispatchesJobs, PreventOverlapping;

    protected $signature = 'reports:populateStats';
    protected $lookBack = 5;
    protected $trackingSource = 'Cake';
    const MAIN_JOB_NAME = 'PopulateEmailCampaignStats';


    public function __construct() {
        parent::__construct();
    }

    public function handle() {
        $date = Carbon::now()->subDay($this->lookBack)->toDateString();
        if (!$this->isRunning(self::MAIN_JOB_NAME)) {
            $this->dispatch(new PopulateEmailCampaignStats(str_random(16)));
            $this->dispatch(new PullCakeDeliverableStats($this->trackingSource, $date, str_random(16)));
            $this->dispatch(new UpdateContentServerStats($this->lookBack, str_random(16)));
        }
    }
}