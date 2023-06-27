<?php

namespace App\Http\Resources\Chat;

use App\Http\Resources\Auth\AuthUserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => 2,
            "author" => new AuthUserResource($this->whenLoaded('user', function () {
                return $this->user;
            })),
            "createdAt" => $this->created_at?->toIso8601String(),
            "text" => $this->text,
            "files" => [],
            "hash" => $this->hash,
            "isReadByRecipient" => $this->is_read_by_recipient,
            "__typename" => "Chat_Message"
        ];
    }
}
