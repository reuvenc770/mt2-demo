<?php

namespace App\Listeners;

use App\Events\NewRecords;
use App\Factories\ServiceFactory;
use App\Services\AttributionRecordTruthService;
use App\Services\EmailFeedAssignmentService;
use App\Services\ScheduledFilterService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewRecordResolver implements ShouldQueue
{
    protected $truthTableService;
    protected $scheduledFilterService;
    protected $assignmentService;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(AttributionRecordTruthService $truthService, EmailFeedAssignmentService $assignmentService)
    {
        $this->truthTableService = $truthService;
        $this->assignmentService = $assignmentService;
    }

    /**
     * Handle the event.
     *
     * @param  NewRecords  $event
     * @return void
     */
    public function handle(NewRecords $event)
    {
        $this->scheduledFilterService = ServiceFactory::createFilterService("expiration");
        $this->truthTableService->insertBulkRecords($event->getEmails());
        $this->assignmentService->insertBulkRecords($event->getEmails());
        $this->scheduledFilterService->insertScheduleFilterBulk($event->getEmails(), 10);

    }
}
