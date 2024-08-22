<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Concerns\RoomUserStatus;
use App\Models\RoomUser;
use App\Traits\ApiResponseTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\DataTables;

class RoomUserController extends Controller
{
    public function adminIndex(Request $request): JsonResponse
    {
        // Generate a unique cache key based on request parameters
        $cacheKey = 'admin_roomusers_' . md5(serialize($request->all()));

          // Check if the result is already cached
        $roomUsers = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($request) {
            return RoomUser::query()
                ->with('rooms', 'users')
                ->when('user_id' == $request->orderBy, fn (Builder $query) => $query->orderBy('user_id', $request->orderType))
                ->when('room_id' == $request->orderBy, fn (Builder $query) => $query->orderBy('room_id', $request->orderType))
                ->when('id' == $request->orderBy, fn (Builder $query) => $query->orderBy('id', $request->orderType))
                ->when('created_at' == $request->orderBy, fn (Builder $query) => $query->orderBy('created_at', $request->orderType))
                ->when('updated_at' == $request->orderBy, fn (Builder $query) => $query->orderBy('updated_at', $request->orderType))
                ->get();
        });

        return DataTables::of($roomUsers)->make(true);
    }

    public function adminCreate(Request $request): JsonResponse
    {
        $roomUser = RoomUser::create([
            'title' => $request->title,
        ]);

        return $this->success([
            'roomUser' => $roomUser,
        ]);
    }

    public function adminShow(RoomUser $roomUser): JsonResponse
    {
        return $this->success([
            'roomUser' => $roomUser,
        ]);
    }

    public function adminUpdate(Request $request, RoomUser $roomUser): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', RoomUserStatus::$list),
        ]);

        $roomUser->update([
            'status' => $request->status,
        ]);

        return $this->success([
            'roomUser' => $roomUser,
        ]);
    }

    public function adminDelete(Request $request, RoomUser $room): JsonResponse
    {
        $room->delete();

        return $this->success([], 'selected toomUser deleted succesfully');
    }
}
