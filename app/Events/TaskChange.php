<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class TaskChange implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user_id;

    public $text;

    public $from_user;

    public $publish_date;

    public $notif_id;

    public $task_id;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user_id, $text, $from_user, $publish_date, $notif_id, $task_id = null)
    {
        $this->user_id = $user_id;
        $this->text = $text;
        $this->from_user = $from_user;
        $this->publish_date = $publish_date;
        $this->notif_id = $notif_id;
        $this->task_id = $task_id;
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
            'message' => $this->text,
            'from_user' => $this->from_user,
            'date' => $this->publish_date,
            'notif_id' => $this->notif_id,
            'task_id' => $this->task_id
        ];
    }
}
