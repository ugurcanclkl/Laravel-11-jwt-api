<?php

use App\Models\Concerns\RoomUserStatus;
use App\Models\Room;
use App\Models\RoomUser;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.ChatRoom.{id}', function (User $user,int $roomId) {
    return  RoomUser::query()->firstWhere([
        'room_id' => $roomId,
        'user_id' => $user->id,
        'status'  => RoomUserStatus::ACCEPTED,
    ])->exists();
});
