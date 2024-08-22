<?php

namespace App\Http\Controllers\Api;

use App\Events\RoomJoinRequestCreatedEvent;
use App\Exceptions\AlreadyJoinedException;
use App\Exceptions\AlreadyWaitingException;
use App\Exceptions\DeniedException;
use App\Http\Controllers\Controller;
use App\Models\Concerns\RoomUserStatus;
use App\Models\Room;
use App\Models\RoomUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\DataTables;

class RoomController extends Controller
{
    public function index(Request $request): JsonResponse 
    {
        // Generate a unique cache key based on request parameters
        $cacheKey = 'rooms_' . md5(serialize($request->all()));

          // Check if the result is already cached
        $messages = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($request) {
            return Room::query()
                ->when($request->has('title'), fn ($q) => $q->where('title', 'ILIKE', '%' . $request->content . '%'))
                ->when('title' == $request->orderBy, fn (Builder $query) => $query->orderBy('user.name', $request->orderType))
                ->when('id' == $request->orderBy, fn (Builder $query) => $query->orderBy('id', $request->orderType))
                ->when('created_at' == $request->orderBy, fn (Builder $query) => $query->orderBy('created_at', $request->orderType))
                ->when('updated_at' == $request->orderBy, fn (Builder $query) => $query->orderBy('updated_at', $request->orderType))
                ->get();
        });

        return $this->success([
            'rooms' =>  DataTables::of($messages)->make(true)
        ]);
    }

    public function join(Request $request, Room $room): JsonResponse
    {
        $user = $request->user();

        $roomUser = RoomUser::firstWhere([
            'room_id' => $room->id,
            'user_id' => $user->id,
        ]);

        if (isset($roomUser)) {
            switch ($roomUser->status) {
                case RoomUserStatus::DENIED:
                    throw new DeniedException();
                case RoomUserStatus::CANCELED:
                    $roomUser->update([
                        'status' => RoomUserStatus::WAITING
                    ]);
                    return $this->success([
                        'roomuser' => $roomUser,
                    ]);
                case RoomUserStatus::ACCEPTED:
                    throw new AlreadyJoinedException();
                case RoomUserStatus::WAITING:
                    throw new AlreadyWaitingException();
            }
        }else{
            $roomUser = $room->roomusers()->create([
                'user_id' => $user->id,
                'status' => RoomUserStatus::WAITING,
            ]);
        }

        //Event to trigger listener for sending mail to admins
        RoomJoinRequestCreatedEvent::dispatch($roomUser);

        return $this->success([
            'roomuser' => $roomUser,
        ]);
    }
}
