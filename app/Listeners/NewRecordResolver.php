<?php

namespace App\Listeners;

use App\Events\NewRecords;
use App\Factories\ServiceFactory;
use App\Services\AttributionRecordTruthService;
use App\Services\EmailFeedAssignmentService;
use App\Services\ScheduledFilterService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Jobs\SetSchedulesJob;
use Illuminate\Foundation\Bus\DispatchesJobs;

class NewRecordResolver implements ShouldQueue
{
    use DispatchesJobs;

    protected $truthTableService;
    protected $scheduledFilterService;
    protected $assignmentService;
    const QUEUE = 'filters';
    const JOB_NAME_BASE = 'NewRecords';
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Handle the event.
     *
     * @param  NewRecords  $event
     * @return void
     */
    public function handle(NewRecords $event)
    {
        $jobName = self::JOB_NAME_BASE . '-' . $event->getId();
        $job = (new SetSchedulesJob($jobName, $event->getEmails(), 'expiration', str_random(16)))->onQueue(self::QUEUE);
        $this->dispatch($job);
    }
}
