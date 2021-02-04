<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user_id;

    public $message;

    public $from_user;

    public $publish_date;

    public $notif_id;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user_id, $message, $from_user, $publish_date, $notif_id)
    {
        $this->user_id = $user_id;
        $this->message = $message;
        $this->from_user = $from_user;
        $this->publish_date = $publish_date;
        $this->notif_id = $notif_id;
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
        return [
            'message' => $this->message,
            'from_user' => $this->from_user,
            'date' => $this->publish_date,
            'notif_id' => $this->notif_id
        ];
    }
}
