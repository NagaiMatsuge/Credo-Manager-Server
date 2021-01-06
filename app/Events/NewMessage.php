<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public $task_id;

    public $files;

    public $user;

    public $to_user;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($task_id, $message, $files, $user, $to_user)
    {
        $this->task_id = $task_id;
        $this->message = $message;
        $this->files = $files;
        $this->user = $user;
        $this->to_user = $to_user;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('new-message-to-' . $this->to_user);
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return ['message' => $this->message, 'files' => $this->files, 'user' => $this->user];
    }
}
