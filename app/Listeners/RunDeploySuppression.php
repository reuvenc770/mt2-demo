<?php

namespace App\Listeners;

use App\Events\ListProfileCompleted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Repositories\DeployRepo;
use Artisan;

class RunDeploySuppression implements ShouldQueue
{

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
            Artisan::call('listprofile:export', ['listProfileId' => $event->getId(), 'offerId' => $row->offer_id]);
        }
        
    }
}
