<?php

namespace App\Listeners;

use App\Events\NewAction;
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
    public function handle(NewAction $event)
    {
        $this->scheduledFilterService = ServiceFactory::createFilterService("activity");
        $this->truthTableService->toggleFieldRecord($event->getEmailId(), $this->scheduledFilterService->fieldName, true);
        $this->scheduledFilterService->insertScheduleFilter($event->getEmailId(), 90);
    }
}