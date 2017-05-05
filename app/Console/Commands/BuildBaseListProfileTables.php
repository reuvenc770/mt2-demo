<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\ListProfileBaseExportJob;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Repositories\ListProfileScheduleRepo;
use Cache;

class BuildBaseListProfileTables extends Command
{
    use DispatchesJobs;
    protected $name = 'StartProfileExports';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'listprofile:baseTables';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Create base tables for today's list profiles.";

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
        $profiles = $repo->getListProfilesForToday();

        $cacheTagName = 'ListProfileBaseTable';
        Cache::forget($cacheTagName);
        Cache::forever($cacheTagName, count($profiles));

        foreach ($profiles as $profileSchedule) {
            $job = new ListProfileBaseExportJob($profileSchedule->list_profile_id, $cacheTagName, str_random(16));
            $this->dispatch($job);
        }
    }
}
