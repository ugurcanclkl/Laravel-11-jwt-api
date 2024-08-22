<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Concerns\RoomUserStatus;
use App\Models\Room;
use App\Models\RoomUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RoomUserController extends Controller
{
    public function index(Request $request): JsonResponse 
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $request->validate([
            'status' => 'nullable|in:' . implode(',', RoomUserStatus::$list),
        ]);

        if(isset($request->status)){
            $roomUsers = $user->roomusers()->get()->where('status', $request->status);
        } else {
           $roomUsers = $user->roomusers();
        }

        return $this->success([
            'roomUsers' => $roomUsers,
        ]);
    }

    public function show(Request $request, RoomUser $roomUser): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $roomUser = $user->roomusers()->get()->firstWhere('id', $roomUser->id);

        return $this->success([
            'roomUsers' => $roomUser,
        ]);
    }

    public function getByRoomId(Request $request, Room $room): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $roomUsers = $user->roomusers()->get()->firstWhere('room_id', $room->id);

        return $this->success([
            'roomUsers' => $roomUsers,
        ]);
    }
}
