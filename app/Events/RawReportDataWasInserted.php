<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

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
    protected $apiName;
    protected $accountNumber;
    /**
     * RawReportDataWasInserted constructor.
     * @param $apiName
     * @param $accountNumber
     * @param $rawReportData
     */
    public function __construct($apiName, $accountNumber, $rawReportData)
    {
        $this->rawReportData = $rawReportData;
        $this->apiName = $apiName;
        $this->accountNumber = $accountNumber;
    }

    /**
     * @return mixed
     */
    public function getRawReportData()
    {
        return $this->rawReportData;
    }

    /**
     * @return mixed
     */
    public function getApiName()
    {
        return $this->apiName;
    }

    /**
     * @return mixed
     */
    public function getAccountNumber()
    {
        return $this->accountNumber;
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
