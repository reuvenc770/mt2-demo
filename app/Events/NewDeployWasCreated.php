<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Models\Deploy;

class NewDeployWasCreated extends Event
{
    use SerializesModels;
    private $deployId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($deployId) {
        $this->deployId = $deployId;
    }
    
    public function getDeployId() {
        return $this->deployId;
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