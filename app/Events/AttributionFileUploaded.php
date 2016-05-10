<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class AttributionFileUploaded extends Event
{
    use SerializesModels;

    protected $filePath;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct( $filePath )
    {
        $this->filePath = $filePath;
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

    public function getFilePath () { return $this->filePath; }
}
