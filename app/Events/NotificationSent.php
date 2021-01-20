<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationSent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user_id;

    public $message;

    public $from_user;

    public $publish_date;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user_id, $message, $from_user, $publish_date)
    {
        $this->user_id = $user_id;
        $this->message = $message;
        $this->from_user = $from_user;
        $this->publish_date = $publish_date;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('notification-to-' . $this->user_id);
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return ['message' => $this->message, 'from_user' => $this->from_user, 'date' => $this->publish_date];
    }
}
