<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 11/8/16
 * Time: 3:12 PM
 */

namespace App\Console\Commands;
use App\Repositories\DeployRepo;
use App\Repositories\ListProfileCombinesRepo;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Jobs\ListProfileBaseExportJob;
use Illuminate\Foundation\Bus\DispatchesJobs;

class BuildScheduledDeploysListProfileBaseTables extends Command
{
    use DispatchesJobs;
    protected $name = 'StartProfileExports';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'listprofile:baseTablesDeploys';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Create base tables for today's Scheduled Deploys";

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
    public function handle(DeployRepo $deployRepo, ListProfileCombinesRepo $listProfileCombinesRepo) {
        $deploys = $deployRepo->getDeploysForToday(Carbon::today()->toDateString());
        foreach($deploys as $deploy){
            $listProfileCombine = $listProfileCombinesRepo->getRowWithListProfiles($deploy->list_profile_id);
            foreach($listProfileCombine->listProfiles as $listProfile){
                $job = new ListProfileBaseExportJob($listProfile->id, str_random(16));
                $this->dispatch($job);
            }
        }
    }
}
