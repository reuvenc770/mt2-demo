<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\ListProfileExportJob;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Repositories\ListProfileScheduleRepo;

class ExportScheduledProfiles extends Command
{
    use DispatchesJobs;
    protected $name = 'StartProfileExports';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'listprofile:exports';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Run today's scheduled list profiles";

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
    public function handle(ListProfileScheduleRepo $repo) {
        $profiles = $repo->getProfilesForToday();

        foreach ($profiles as $profileSchedules) {
            $job = new ListProfileExportJob($profile->id, str_random(16));
        }
    }
}
