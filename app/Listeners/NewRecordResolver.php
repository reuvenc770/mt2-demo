<?php

namespace App\Listeners;

use App\Events\NewRecord;
use App\Factories\ServiceFactory;
use App\Services\AttributionRecordTruthService;
use App\Services\EmailRecordService;
use App\Services\ScheduledFilterService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

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
     * @param  NewRecord  $event
     * @return void
     */
    public function handle(NewRecord $event)
    {
        $this->scheduledFilterService =  ServiceFactory::createFilterService("expiration");
        if(!$this->emailService->getEmailAddress($event->getEmailId()) && $event->getEmailId() != 0) {
            $this->truthTableService->insertRecord($event->getEmailId());
            $this->scheduledFilterService->insertScheduleFilter($event->getEmailId(), 10);
        }
    }
}
