<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Traits\ApiResponseTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\DataTables;

class RoomController extends Controller
{
    
    public function adminIndex(Request $request): JsonResponse
    {
        // Generate a unique cache key based on request parameters
        $cacheKey = 'admin_rooms_' . md5(serialize($request->all()));

          // Check if the result is already cached
        $messages = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($request) {
            return Room::query()
                ->with('messages', 'users', 'roomusers')
                ->when($request->has('title'), fn ($q) => $q->where('title', 'ILIKE', '%' . $request->content . '%'))
                ->when('title' == $request->orderBy, fn (Builder $query) => $query->orderBy('user.name', $request->orderType))
                ->when('id' == $request->orderBy, fn (Builder $query) => $query->orderBy('id', $request->orderType))
                ->when('created_at' == $request->orderBy, fn (Builder $query) => $query->orderBy('created_at', $request->orderType))
                ->when('updated_at' == $request->orderBy, fn (Builder $query) => $query->orderBy('updated_at', $request->orderType))
                ->get();
        });

        return DataTables::of($messages)->make(true);
    }

    public function adminCreate(Request $request): JsonResponse
    {
        $room = Room::create([
            'title' => $request->title,
        ]);

        return $this->success([
            'room' => $room,
        ]);
    }

    public function adminShow(Room $room): JsonResponse
    {
        return $this->success([
            'room' => $room,
        ]);
    }

    public function adminUpdate(Request $request, Room $room): JsonResponse
    {
        $request->validate([
            'title' => 'required|string'
        ]);

        $room = $room->update([
            'title' => $request->content,
        ]);

        return $this->success([
            'room' => $room,
        ]);
    }

    public function adminDelete(Request $request, Room $room): JsonResponse
    {
        $room->delete();

        return $this->success([], 'selected message deleted succesfully');
    }
}
