<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 11/11/16
 * Time: 3:05 PM
 */

namespace App\Listeners;

use App\Events\ListProfileCompleted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Repositories\DeployRepo;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Jobs\ExportListProfileJob;

class ExportFinishedListProfile implements ShouldQueue
{
    use DispatchesJobs;



    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct() {

    }

    /**
     * Handle the event.
     *
     * @param  ListProfileCompleted  $event
     * @return void
     */
    public function handle(ListProfileCompleted $event) {
            $job = new ExportListProfileJob($event->getId(), array(), str_random(16));
            $this->dispatch($job);
        }

}