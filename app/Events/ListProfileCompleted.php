<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ListProfileCompleted extends Event
{
    use SerializesModels;

    private $id;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($id) {
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
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
