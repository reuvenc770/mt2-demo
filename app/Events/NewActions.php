<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewActions extends Event
{
    use SerializesModels;
    protected $emails;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($emails)
    {
        $this->emails = $emails;

    }

    /**
     * @return mixed
     */
    public function getEmails()
    {
        return $this->emails;
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
