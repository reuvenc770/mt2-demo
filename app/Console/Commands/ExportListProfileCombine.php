<?php

namespace App\Console\Commands;

use App\Jobs\ExportListProfileCombineJob;
use App\Repositories\DeployRepo;
use App\Repositories\ListProfileCombinesRepo;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Carbon\Carbon;
class ExportListProfileCombine extends Command
{

    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'listprofile:export {mode} {listCombineId?} {offerId?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create list profile exports using the proper suppression lists.';

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
    public function handle(DeployRepo $deployRepo, ListProfileCombinesRepo $listProfileCombinesRepo) {
        $mode = $this->argument('mode');
        switch ($mode){
            case "single":
                $listCombineId = $this->argument('listCombineId');
                $offerId = $this->argument('offerId');
                $job = new ExportListProfileCombineJob($listCombineId, $offerId, str_random(16));
                $this->dispatch($job);
                break;

            case "daily":
                $deploys = $deployRepo->getDeploysForToday(Carbon::today()->toDateString());
                foreach($deploys as $deploy){
                    $combine = $listProfileCombinesRepo->getRowWithListProfiles($deploy->list_profile_id);

                    foreach($combine->listProfiles as $listProfile){
                        $job = new ExportListProfileCombineJob($listProfile->id, $deploy->offer_id, str_random(16));
                        $this->dispatch($job);
                    }
                }
                break;
            default:
                throw new \Exception("Type of export does not exist");
                break;
        }
    }
}
