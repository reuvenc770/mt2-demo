<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Log;

/**
 * Class RawReportDataWasInserted
 * @package App\Events
 */
class RawTrackingDataWasInserted extends Event
{
    use SerializesModels;

    /**
     * @var
     */
    protected $rawTrackingData;
    protected $source;
    /**
     * RawReportDataWasInserted constructor.
     * @param $apiName
     * @param $accountNumber
     * @param $rawReportData
     */
    public function __construct($source, $rawTrackingData)
    {
        $this->rawTrackingData = $rawTrackingData;
        $this->source = $source;
    }

    /**
     * @return mixed
     */
    public function getRawTrackingData()
    {
        return $this->rawTrackingData;
    }

    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->source;
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
