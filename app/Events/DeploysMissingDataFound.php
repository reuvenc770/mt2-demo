<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;

class DeploysMissingDataFound extends Event
{
    use SerializesModels, ShouldQueue;
    private $specifiedDeploys;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($specifiedDeploys) {
        $this->specifiedDeploys = $specifiedDeploys;
    }

    public function getSpecifiedDeploys() {
        return $this->specifiedDeploys;
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
