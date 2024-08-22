<?php

namespace App\Http\Controllers\Api\Admin;

use App\Events\MessageSentEvent;
use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Room;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\DataTables;

class MessageController extends Controller
{
    public function adminIndex(Request $request): JsonResponse
    {
        // Generate a unique cache key based on request parameters
        $cacheKey = 'admin_messages_' . md5(serialize($request->all()));

          // Check if the result is already cached
        $messages = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($request) {
            return Message::query()
                ->with('room', 'user')
                ->when($request->has('content'), fn ($q) => $q->where('content', 'ILIKE', '%' . $request->content . '%'))
                ->when($request->has('username'), fn ($q) => $q->whereHas('user', fn($q) => $q->where('user.name', 'ILIKE', '%' . $request->username . '%')))
                ->when('name' == $request->orderBy, fn (Builder $query) => $query->orderBy('user.name', $request->orderType))
                ->when('email' == $request->orderBy, fn (Builder $query) => $query->orderBy('user.email', $request->orderType))
                ->when('id' == $request->orderBy, fn (Builder $query) => $query->orderBy('id', $request->orderType))
                ->when('user_id' == $request->orderBy, fn (Builder $query) => $query->orderBy('user.id', $request->orderType))
                ->when('created_at' == $request->orderBy, fn (Builder $query) => $query->orderBy('created_at', $request->orderType))
                ->when('updated_at' == $request->orderBy, fn (Builder $query) => $query->orderBy('updated_at', $request->orderType))
                ->get();
        });

        return DataTables::of($messages)->make(true);
    }

    public function adminCreate(Request $request, Room $room): JsonResponse
    {
        $user = $request->user();

        $message = $room->messages()->create([
            'user_id' => $user->id,
            'content' => $request->content,
        ]);

        MessageSentEvent::dispatch($message);

        return $this->success([
            'message' => $message,
        ]);
    }

    public function adminShow(Message $message): JsonResponse
    {
        return $this->success([
            'message' => $message,
        ]);
    }

    public function adminUpdate(Request $request, Message $message): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'content' => 'required|string'
        ]);

        $message = $message->update([
            'content' => $request->content,
        ]);
        return $this->success([
            'message' => $message,
        ]);
    }

    public function adminDelete(Request $request, Message $message): JsonResponse
    {
        $message->delete();

        return $this->success([], 'selected message deleted succesfully');
    }
}
