<?php

namespace App\Listeners;

use App\Events\RoomJoinRequestCreatedEvent;
use App\Mail\RoomJoinRequestCreated;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Yajra\Acl\Models\Role;

class RoomJoinRequestCreatedNotification implements ShouldQueue
{
    use Queueable, InteractsWithQueue;

    /**
     * The number of times the queued listener may be attempted.
     *
     * @var int
     */
    public $tries = 5;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(RoomJoinRequestCreatedEvent $event): void
    {
        Mail::to("developer@mail.com")->send(new RoomJoinRequestCreated($event->roomUser));
    }
}
