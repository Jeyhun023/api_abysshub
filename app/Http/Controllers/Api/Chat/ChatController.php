<?php

namespace App\Http\Controllers\Api\Chat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Events\NewChatMessageEvent;
use App\Models\ChatUser;
use App\Models\ChatMessage;
use App\Http\Requests\Api\Chat\ChatCheckRequest;
use App\Http\Requests\Api\Chat\ChatMessageRequest;
use App\Http\Requests\Api\Chat\ChatLoadRequest;
use App\Http\Resources\Chat\ChatResource;
use App\Http\Resources\Chat\MessageResource;
use App\Http\Resources\Chat\MessageCollection;
use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;

class ChatController extends Controller
{
    use ApiResponser;
    public $user;
    
    public function __construct()
    {
        $this->user = auth('api')->user();
    }

    public function check(ChatCheckRequest $request)
    {
        $newChat = ChatUser::where(['user_id_from' => $this->user->id, 'user_id_to' => $request->user])
            ->orWhere(['user_id_from' => $request->user, 'user_id_to' => $this->user->id])
            ->with('messages.user')
            ->first();
        
        if(!$newChat){
            $newChat = new ChatUser();
            $newChat->user_id_from = $this->user->id;
            $newChat->user_id_to = $request->user;
            $newChat->last_activity = now();
            $newChat->save();
        }
        
        $newChat->messages = $newChat->messages->reverse();

        return $this->successResponse(new ChatResource($newChat));
    }

    public function sendMessage($chat, ChatMessageRequest $request)
    {
        $newMessage = new ChatMessage();
        $newMessage->chat_id = $chat;
        $newMessage->user_id = auth()->id();
        $newMessage->content = $request->content;
        $newMessage->save();

        broadcast(new NewChatMessageEvent ($newMessage))->toOthers();

        return $this->successResponse(new MessageResource($newMessage));
    }

    public function loadMessage($chat, $limit, ChatLoadRequest $request)
    {
        $loadMessage = ChatMessage::where('chat_id', $chat)
            ->where('id', '<', $limit)
            ->with('user')
            ->latest('id')
            ->take(15)
            ->get();

        return $this->successResponse(new MessageCollection($loadMessage->reverse()));
    }

}
