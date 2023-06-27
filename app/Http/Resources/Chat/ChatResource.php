<?php

namespace App\Http\Resources\Chat;

use App\Http\Resources\Auth\AuthUserResource;
use App\Http\Resources\Ticket\TicketResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "title" => $this->title,
            "ticketUser" => new AuthUserResource($this->whenLoaded('ticketUser', function () {
                return $this->ticketUser;
            })),
            "lastMessage"=>new MessageResource($this->whenLoaded('messages',function (){
                return $this->messages->last();
            })),
            "replyUser"=>new AuthUserResource($this->whenLoaded('replyUser', function () {
                return $this->replyUser;
            })),
            "lastUpdatedAt"=>$this->last_update_at?->toIso8601String(),
            "ticket"=>new TicketResource($this->whenLoaded('ticket', function (){
                return $this->ticket;
            })),
            "ticketId"=>$this->ticket_id,
            "unreadMessagesCountByLastMessageRecipient"=>$this->unread_messages_count_by_last_message_recipient,
            "__typename"=> "Chat_Chat"
        ];
    }
}
