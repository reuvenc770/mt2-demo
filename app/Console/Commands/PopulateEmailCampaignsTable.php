<?php

namespace App\Console\Commands;


use App\Repositories\EspApiAccountRepo;
use Carbon\Carbon;
use App\Jobs\PopulateEmailCampaignStats;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;

class PopulateEmailCampaignsTable extends Command {
    use DispatchesJobs;

    protected $signature = 'reports:populateStats {lookBack?}';
    protected $lookBack;
    protected $description = 'Populate email_campaign_statistics';


    public function __construct(EspApiAccountRepo $espRepo) {
        parent::__construct();
        $this->espRepo = $espRepo;
    }

    public function handle() {
        $lookBack = $this->argument('lookBack') ? $this->argument('lookBack') 
            : env('LOOKBACK',2);

        $this->dispatch(new PopulateEmailCampaignStats($lookBack, str_random(16)));

    }
}