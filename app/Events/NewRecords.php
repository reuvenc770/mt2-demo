<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewRecords extends Event
{
    use SerializesModels;
    protected $emails;
    private $id;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($emails, $id)
    {
        $this->emails = $emails;
        $this->id = $id;
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

    public function getId()
    {
        return $this->id;
    }
}
