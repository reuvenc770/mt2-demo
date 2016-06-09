<?php

namespace App\Listeners;

use App\Events\DeploysMissingDataFound;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Repositories\DeployRecordRerunRepo;
use Artisan;

class RerunDeployStats
{

    private $rerunRepo;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(DeployRecordRerunRepo $rerunRepo) {
        $this->rerunRepo = $rerunRepo;
    }

    /**
     * Handle the event.
     *
     * @param  DeploysMissingDataFound  $event
     * @return void
     */
    public function handle(DeploysMissingDataFound $event) {

        // Will eventually want to use $event->getSpecifiedDeploys()
        // to rerun user-specified deploys

        $esps = $this->rerunRepo->getEsps();

        foreach($esps as $esp) {
            if ($esp) {
                echo $esp->name . PHP_EOL;

                Artisan::call('reports:rerunDeliverables', ['espName' => $esp->name]);

            }   
        }
        
    }
}
