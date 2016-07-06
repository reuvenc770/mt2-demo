<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewRecord extends Event
{
    use SerializesModels;
    protected $emailId;
    protected $clientId;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($emailId, $clientId)
    {
        $this->emailId = $emailId;
        $this->clientId = $clientId;
    }

    /**
     * @return mixed
     */
    public function getEmailId()
    {
        return $this->emailId;
    }

    /**
     * @return mixed
     */
    public function getClientId()
    {
        return $this->clientId;
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
