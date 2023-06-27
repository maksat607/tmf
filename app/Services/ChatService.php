<?php

namespace App\Services;

use App\Models\ChatChat;
use App\Models\TicketBaseTicket;
use App\Traits\ChatTitleResolverTrait;
use Illuminate\Support\Facades\DB;

class ChatService
{
    use ChatTitleResolverTrait;

    public function create()
    {
        return DB::transaction(function () {
            $ticket = TicketBaseTicket::findOrFail(request()->input('ticket'));

            $chat = ChatChat::create([
                'title' => $this->resolveChatTitleByTicket($ticket->ticketAirplaneTicket),
                'unread_messages_count_by_last_message_recipient' => 1,
                'last_update_at' => now(),
                'created_at' => now(),
                'ticket_id' => $ticket->id,
                'ticket_user_id' => $ticket->user_id,
                'reply_user_id' => auth()->id(),
            ]);
            $this->createMessage($chat);
            return $chat;
        });
    }

    public function createMessage(ChatChat $chat)
    {
        $message = $chat->messages()->create([
            'text' => request()->get('text'),
            'hash' => request()->get('hash'),
            'is_read_by_recipient' => false,
            'created_at' => now(),
            'user_id' => auth()->id(),
        ]);
        $message->load('user');
        return $message;
    }


}
