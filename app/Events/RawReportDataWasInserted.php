<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Log;
use App\Services\Interfaces\IDataService;

/**
 * Class RawReportDataWasInserted
 * @package App\Events
 */
class RawReportDataWasInserted extends Event
{
    use SerializesModels;

    /**
     * @var
     */
    protected $rawReportData;
    protected $service;
    
    /**
     * RawReportDataWasInserted constructor.
     * @param $apiName
     * @param $accountNumber
     * @param $rawReportData
     */
    public function __construct(IDataService &$service, $rawReportData)
    {
        $this->rawReportData = $rawReportData;
        $this->service = $service;
    }

    /**
     * @return mixed
     */
    public function getRawReportData()
    {
        return $this->rawReportData;
    }

    /**
     * @return ITrackingService
     */

    public function getService() {
        return $this->service;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
