<?php

namespace App\Console\Commands;

use App\Repositories\ListProfileScheduleRepo;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Jobs\ExportListProfileJob;

class ExportScheduledListProfile extends Command
{

    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'listprofile:exportScheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export Scheduled List Profiles to the FTP';

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
    public function handle(ListProfileScheduleRepo $scheduleRepo) {
        $listProfilesForToday = $scheduleRepo->getProfilesForToday();
        foreach($listProfilesForToday as $listProfile) {
            $job = new ExportListProfileJob($listProfile->list_profile_id, array(), str_random(16));//blank array to skip suppression
            $this->dispatch($job);
        }

    }
}
