<?php

namespace App\Events;

use App\Models\RoomUser;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RoomJoinRequestCreatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $roomUser;

    /**
     * Create a new event instance.
     */
    public function __construct(RoomUser $roomUser)
    {
        $this->roomUser = $roomUser;
    }
}
