<?php

namespace App\Console\Commands;

use App\Repositories\DeployRepo;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Jobs\ListProfileCombineExportJob;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Repositories\ListProfileScheduleRepo;

class BuildDeployProfileBaseTables extends Command
{
    use DispatchesJobs;
    protected $name = 'StartDeployProfileExports';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'listprofile:DeployBaseTables';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Create base tables for List Profiles in todays deploy.";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(DeployRepo $deployRepo, ListProfileScheduleRepo $scheduleRepo) {
        $deploys = $deployRepo->getDeploysForToday(Carbon::today()->toDateString());
        $schedules = $scheduleRepo->getScheduledProfilesForToday()->pluck('list_profile_id');
        foreach ($deploys as $deploy) {
            $job = new ListProfileCombineExportJob($deploy->list_profile_combine_id, str_random(16), $deploy->offer_id, $schedules);
            $this->dispatch($job);
        }
    }
}
