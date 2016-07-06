<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewAction extends Event
{
    use SerializesModels;
    protected $emailId;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($emailId)
    {
        $this->emailId = $emailId;

    }

    /**
     * @return mixed
     */
    public function getEmailId()
    {
        return $this->emailId;
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
