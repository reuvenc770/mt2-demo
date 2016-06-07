<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class BulkSuppressionFileWasUploaded extends Event
{
    use SerializesModels;
    protected $reasonId;
    protected $fileName;
    protected $date;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($reasonId, $fileName, $date)
    {
        $this->reasonId = $reasonId;
        $this->fileName = $fileName;
        $this->date = $date;
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

    /**
     * @return mixed
     */
    public function getReasonId()
    {
        return $this->reasonId;
    }

    /**
     * @return mixed
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }
}
