<?php

namespace App\Listeners;

use App\Events\NewActions;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\AttributionRecordTruthService;
use App\Factories\ServiceFactory;
//TODO If we get more filters like these we 
class NewActionResolver implements ShouldQueue
{
    protected $truthTableService;
    protected $scheduledFilterService;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(AttributionRecordTruthService $truthService)
    {
        $this->truthTableService = $truthService;
    }

    /**
     * Handle the event.
     *
     * @param  NewRecord $event
     * @return void
     */
    public function handle(NewActions $event)
    {
        $this->scheduledFilterService = ServiceFactory::createFilterService("activity");
        $this->truthTableService->bulkToggleFieldRecord($event->getEmails(), $this->scheduledFilterService->fieldName, true);
        $this->scheduledFilterService->insertScheduleFilterBulk($event->getEmails(), 90);
    }
}