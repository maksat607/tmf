<?php

namespace App\Http\Resources\Auth;



use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthUserResource extends JsonResource
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
            "name" => $this->name,
            "surname" => $this->surname,
            "photo" => new UserAuthPhotoResource($this->whenLoaded('photo', function () {
                return $this->photo;
            })),
            "accessToken" => new AccessTokenResource($this->whenLoaded('accessTokens', function () {
                return ($this->accessTokens()->orderBy('created_at','desc')->first());
            })),
            "increasesCount" => $this->increases_count,
            "decreasesCount" => $this->decreases_count,
            "email" => $this->email,
            "emailVerified" => $this->email_verified ?? false,
            "__typename" => "Auth_User"
        ];
    }
}
