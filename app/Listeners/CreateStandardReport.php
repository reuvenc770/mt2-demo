<?php

namespace App\Listeners;

use App\Events\RawReportDataWasInserted;
use App\Factories\APIFactory;
use Illuminate\Contracts\Queue\ShouldQueue;
use League\Flysystem\Exception;
use Maknz\Slack\Facades\Slack;
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
        $standardService = APIFactory::createStandardReportService($service);

        foreach ($event->getRawReportData() as $report) {
            try {
                $standardReport = $service->mapToStandardReport($report);
                $standardService->insertStandardStats($standardReport);
            } catch (Exception $e) {
                Slack::to('#mt2-dev-failed-jobs')->send("Map to Standard has failed for ESP ACCOUNT: {$report->esp_account_id}");
            }

        }
    }
}
