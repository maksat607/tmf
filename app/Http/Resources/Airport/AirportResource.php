<?php

namespace App\Http\Resources\Airport;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AirportResource extends JsonResource
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
            "code" => $this->code,
            "name" => $this->name,
            "countryCode" => $this->country_code,
            "countryName" => $this->country_name,
            "cityCode" => $this->city_code,
            "latitude" => $this->latitude,
            "longitude" => $this->longitude,
            "tzOffset" => $this->tz_offset,
            "translations" => $this->translations,
            '__typename' => 'Dictionary_Airports'
        ];
    }
}
