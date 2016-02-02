<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Log;

/**
 * Class RawTrackingDataWasInserted
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
    protected $startDate;
    protected $endDate;
    /**
     * RawTrackingWasInserted constructor.
     * @param $apiName
     * @param $accountNumber
     * @param $rawReportData
     */
    public function __construct($source, $start, $end, $rawTrackingData) {
        $this->rawTrackingData = $rawTrackingData;
        $this->source = $source;
        $this->startDate = $start;
        $this->endDate = $end;
    }

    /**
     * @return mixed
     */
    public function getRawTrackingData() {
        return $this->rawTrackingData;
    }

    /**
     * @return string
     */
    public function getSource() {
        return $this->source;
    }

    /**
     * @return string
     */
    public function getStartDate() {
        return $this->startDate;
    }

    /**
     * @return string
     */
    public function getEndDate() {
        return $this->endDate;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn() {
        return [];
    }
}
