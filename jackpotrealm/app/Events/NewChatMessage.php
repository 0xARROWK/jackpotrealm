<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewChatMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $message;
    public string $user;
    public string $id;
    public string $color;
    public array $role;
    public string $mid;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(string $id, string $user, string $message, string $color, array $role, string $mid)
    {
        $this->id = $id;
        $this->user = $user;
        $this->message = $message;
        $this->color = $color;
        $this->role = $role;
        $this->mid = $mid;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel
     */
    public function broadcastOn(): Channel
    {
        return new Channel('chat');
    }
}
