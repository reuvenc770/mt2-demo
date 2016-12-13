<?php

namespace App\Listeners;

use App\Events\NewActions;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\AttributionRecordTruthService;
use App\Factories\ServiceFactory;
use App\Jobs\SetSchedulesJob;
use Illuminate\Foundation\Bus\DispatchesJobs;

class NewActionResolver implements ShouldQueue
{
    use DispatchesJobs;

    protected $truthTableService;
    protected $scheduledFilterService;
    const QUEUE = 'filters';
    const JOB_NAME_BASE = 'NewActions';

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Handle the event.
     *
     * @param  NewRecord $event
     * @return void
     */
    public function handle(NewActions $event)
    {
        $jobName = self::JOB_NAME_BASE . '-' . $event->getId();
        $job = (new SetSchedulesJob($jobName, $event->getEmails(), 'activity', str_random(16)))->onQueue(self::QUEUE);
        $this->dispatch($job);
    }
}