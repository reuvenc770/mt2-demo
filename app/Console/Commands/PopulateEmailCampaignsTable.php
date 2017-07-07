<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Jobs\DataProcessingJob;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Console\Traits\PreventOverlapping;

class PopulateEmailCampaignsTable extends Command {
    use DispatchesJobs, PreventOverlapping;

    protected $signature = 'reports:populateStats';
    protected $description = 'Populate the email_campaign_statistics aggregate table';
    protected $lookBack = 5;
    protected $trackingSource = 'Cake';
    private $jobs = ['PopulateEmailCampaignStats', 'PullCakeDeliverableStats'];

    public function __construct() {
        parent::__construct();
    }

    public function handle() {
        foreach ($this->jobs as $job) {
            $this->dispatch(new DataProcessingJob($job, str_random(16)));
        }
    }
}
