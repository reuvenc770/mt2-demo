<?php

namespace App\Listeners;

use App\Events\DeploysMissingDataFound;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Repositories\DeployRecordRerunRepo;

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
        if ($event->getSpecifiedDeploys()) {
            // rerun only specified deploys

            // Somehow pass these in ... mark them?

        }
        else {
            // rerun all stored deploys

            /*
                1. get all esps that appear in the rerun table
                2. have a hard-coded list?
            */
            echo "Running esp check:" . PHP_EOL;
            $esps = $this->rerunRepo->getEsps();

            foreach($esps as $esp) {
                if ($esp) {
                    echo $esp->name . PHP_EOL;

                    /* 
                        either manually run each job with
                        Artisan::call('reports:downloadDeliverables',
                            [
                                'espName' => $esp->name . ':rerun',
                                'lookback' => 31,
                                'queue' => //depends. usually $esp->name
                            ]
                        );
                    */ 

                }
                
            }
        }
    }

}
