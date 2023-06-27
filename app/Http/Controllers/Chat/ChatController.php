<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Http\Resources\Chat\ChatResource;
use App\Http\Resources\Chat\MessageResource;
use App\Models\AuthUser;
use App\Models\ChatChat;
use App\Models\TicketBaseTicket;
use App\Services\ChatService;
use App\Traits\ChatTitleResolverTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ChatController extends Controller
{
    public $chatService;
    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    public function my(Request $request)
    {
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 10);
        $user = auth()->user();


        $query = ChatChat::with([
            'ticketUser', 'replyUser', 'messages', 'ticket'
        ])->where(function ($query) use ($user) {
            $query->where('ticket_user_id', $user->id)
                ->orWhere('reply_user_id', $user->id);
        });
        $count = $query->count();
        $items = $query->offset($offset)
            ->limit($limit)
            ->orderBy('last_update_at', 'desc')
            ->get();

        $chats = ChatResource::collection($items);
        return Response::json($chats)->header('X-Total-Count', $count);
    }

    public function show(ChatChat $chat)
    {
        if ($chat->replyUser->id !== auth()->id() && $chat->ticketUser->id !== auth()->id()) {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        return new ChatResource($chat->load([
            'ticketUser', 'replyUser', 'messages', 'ticket'
        ]));
    }

    public function makeRead(ChatChat $chat)
    {
        if ($chat->replyUser->id !== auth()->id() && $chat->ticketUser->id !== auth()->id()) {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        $chat->messages()->update(['is_read_by_recipient' => 1]);

        return new ChatResource($chat->load([
            'ticketUser', 'replyUser', 'messages', 'ticket'
        ]));
    }

    public function ticketChats(TicketBaseTicket $ticket, Request $request)
    {
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 10);


        $query = $ticket->chats()->with([
            'ticketUser', 'replyUser', 'messages', 'ticket'
        ])->offset($offset)
            ->limit($limit)
            ->orderBy('last_update_at', 'desc');

        $count = $query->count();
        $items = $query->get();

        $chats = ChatResource::collection($items);
        return Response::json($chats)->header('X-Total-Count', $count);
    }

    public function detailsByTicketAndReplyUser(TicketBaseTicket $ticket, AuthUser $replyUser)
    {
        $chat = $ticket
            ->chats()
            ->with([
                'ticketUser', 'replyUser', 'messages', 'ticket'
            ])
            ->where('reply_user_id', $replyUser->id)
            ->firstOrFail();

        return new ChatResource($chat);
    }

    public function createChat(Request $request)
    {

        $chat = ChatChat::where('ticket_id', $request->get('ticket'))->where('reply_user_id', auth()->id())->first();
        if ($chat) {
            return response()->json(['error' => 'Chat already exists'], 400);
        }
        $chat = $this->chatService->create();
        return new ChatResource($chat->load([
            'ticketUser', 'replyUser', 'messages', 'ticket'
        ]));
    }

    public function messages(Request $request,ChatChat $chat){
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 10);

        $messages = $chat->messages()
            ->with('user')
            ->offset($offset)
            ->limit($limit)
            ->get();
        return MessageResource::collection($messages);
    }

    public function createChatMessage(Request $request,ChatChat $chat){
        $message = $this->chatService->createMessage($chat);
        return new MessageResource($message);
    }
}
