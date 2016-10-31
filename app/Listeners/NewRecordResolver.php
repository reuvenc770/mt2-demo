<?php

namespace App\Listeners;

use App\Events\NewRecords;
use App\Factories\ServiceFactory;
use App\Services\AttributionRecordTruthService;
use App\Services\EmailRecordService;
use App\Services\ScheduledFilterService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;
class NewRecordResolver implements ShouldQueue
{
    protected $truthTableService;
    protected $scheduledFilterService;
    protected $emailService;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(AttributionRecordTruthService $truthService, EmailRecordService $recordService)
    {
        $this->truthTableService = $truthService;
        $this->emailService = $recordService;
    }

    /**
     * Handle the event.
     *
     * @param  NewRecords  $event
     * @return void
     */
    public function handle(NewRecords $event)
    {
        $this->scheduledFilterService =  ServiceFactory::createFilterService("expiration");
        $this->truthTableService->insertBulkRecords($event->getEmails());
        $this->scheduledFilterService->insertScheduleFilterBulk($event->getEmails(), 10);
    }
}
