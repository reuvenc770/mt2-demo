<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Models\Deploy;

class NewDeployWasCreated extends Event
{
    use SerializesModels;
    private $specifiedDeploys;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Deploy $deploy) {
        $this->deploy = $deploy;
    }
    
    public function getDeploy() {
        return $this->deploy;
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