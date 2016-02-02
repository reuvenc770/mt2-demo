<?php

namespace App\Listeners;

use App\Events\RawTrackingDataWasInserted;
use App\Factories\APIFactory;
use Illuminate\Contracts\Queue\ShouldQueue;
use League\Flysystem\Exception;

/**
 * Class UpdateStandardReportWithTrackingData
 * @package App\Listeners
 */
class UpdateStandardReportWithTrackingData implements ShouldQueue
{
    /**
     *
     */
    const SERVICE_NAME = "Standard";

    /**
     * Convert Raw Reports into Standard Reports
     * @param RawReportDataWasInserted $event
     * @throws \Exception
     */
    public function handle(RawTrackingDataWasInserted $event)
    {
        /*
        Currently a stub. Will need to be redone to handle new table

        $service = APIFactory::createTrackingApiService($event->getSource(), $event->getStartDate(), $event->getEndDate());
        $standardService = APIFactory::createApiReportService(self::SERVICE_NAME, $event->getAccountNumber());
        foreach ($event->getRawReportData() as $report) {
            try {
                $standardReport = $service->mapToStandardReport($report);
                $standardService->insertStandardStats($standardReport);
            } catch (Exception $e) {
                throw new \Exception($e->getMessage());
            }

        }
        */
    }
}
