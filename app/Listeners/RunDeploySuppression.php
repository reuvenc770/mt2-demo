<?php

namespace App\Listeners;

use App\Events\ListProfileCompleted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Repositories\DeployRepo;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Jobs\ExportListProfileJob;

class RunDeploySuppression implements ShouldQueue
{
    use DispatchesJobs;

    private $repo;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(DeployRepo $repo) {
        $this->repo = $repo;
    }

    /**
     * Handle the event.
     *
     * @param  ListProfileCompleted  $event
     * @return void
     */
    public function handle(ListProfileCompleted $event) {
        $deploys = $this->repo->getOffersForTodayWithListProfile($event->getId());

        foreach($deploys as $row) {
            $job = new ExportListProfileJob($event->getId(), $row->offer_id, str_random(16));
            $this->dispatch($job);
        }
        
    }
}
