<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 11/11/16
 * Time: 9:33 AM
 */

namespace App\Console\Commands;
use App\Jobs\ExportListProfileCombineJob;
use App\Repositories\DeployRepo;
use Carbon\Carbon;
use Illuminate\Foundation\Bus\DispatchesJobs;

class ExportScheduledDeployListProfiles
{
    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'listprofile:exportDeploys';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export Deploy List Profiles to the FTP';

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
    public function handle(DeployRepo $deployRepo) {
        $deploys = $deployRepo->getDeploysForToday(Carbon::today()->toDateString());

        foreach($deploys as $deploy) {
            $job = new ExportListProfileCombineJob($deploy->list_combine_id, $deploy->offer_id, str_random(16));
            $this->dispatch($job);
        }

    }
}