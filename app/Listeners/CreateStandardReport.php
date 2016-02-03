<?php

namespace App\Listeners;

use App\Events\RawReportDataWasInserted;
use App\Factories\APIFactory;
use Illuminate\Contracts\Queue\ShouldQueue;
use League\Flysystem\Exception;

/**
 * Class CreateStandardReport
 * @package App\Listeners
 */
class CreateStandardReport implements ShouldQueue
{

    /**
     * Convert Raw Reports into Standard Reports
     * @param RawReportDataWasInserted $event
     * @throws \Exception
     */
    public function handle(RawReportDataWasInserted $event)
    {

        $service = $event->getService();
        $standardService = APIFactory::createStandardReportService();

        foreach ($event->getRawReportData() as $report) {
            try {
                $standardReport = $service->mapToStandardReport($report);
                $standardService->insertStandardStats($standardReport);
            } catch (Exception $e) {
                throw new \Exception($e->getMessage());
            }

        }
    }
}
