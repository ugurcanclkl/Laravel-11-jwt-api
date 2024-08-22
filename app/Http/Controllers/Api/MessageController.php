<?php

namespace App\Http\Controllers\Api;

use App\Events\MessageSentEvent;
use App\Exceptions\Concerns\ErrorStatusCode;
use App\Http\Controllers\Controller;
use App\Models\Concerns\RoomUserStatus;
use App\Models\Message;
use App\Models\Room;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\DataTables;

class MessageController extends Controller
{
    public function index(Request $request, Room $assignedRoom): JsonResponse
    {
        $user = $request->user();

        $messages = $user->messages()->get()->where('room_id', $assignedRoom);

        return $this->success([
            'messages' => $messages,
        ]);
    }

    public function send(Request $request, Room $assignedRoom): JsonResponse
    {
        $user = $request->user();

        $access = $user->roomusers()->get()->firstWhere([
            'room_id' => $assignedRoom->id,
            'status'  => RoomUserStatus::ACCEPTED,
        ]);

        if(!$access) {
            return $this->error(
                __('api.unauthorized'),
                [
                    'status_code' => ErrorStatusCode::UNAUTHORIZED,
                ],
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        $request->validate([
            'content' => 'required|string'
        ]);

        $message = $assignedRoom->messages()->create([
            'user_id' => $user->id,
            'content' => $request->content,
        ]);

        MessageSentEvent::dispatch($message);

        return $this->success([
            'message' => $message,
        ]);
    }

    public function update(Request $request, Room $assignedRoom, Message $userMessage): JsonResponse
    {
        $request->validate([
            'content' => 'required|string'
        ]);

        $userMessage->update([
            'content' => $request->content,
        ]);

        return $this->success([
            'message' => $userMessage,                                        
        ]);
    }

    public function delete(Message $message): JsonResponse
    {

        $message->delete();

        return $this->success([], 'your message deleted succesfully');
    }
}
