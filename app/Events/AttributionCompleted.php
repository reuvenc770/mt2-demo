<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class AttributionCompleted extends Event
{
    use SerializesModels;

    protected $modelId;
    protected $userEmail;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($modelId,$userEmail)
    {
        $this->modelId = $modelId;
        $this->userEmail = $userEmail;
    }

    public function getModelId () { return $this->modelId; }

    public function getUserEmail () { return $this->userEmail; }

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
