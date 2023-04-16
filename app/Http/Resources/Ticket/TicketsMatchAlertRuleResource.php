<?php

namespace App\Http\Resources\Ticket;

use App\Http\Resources\Airport\AirportResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketsMatchAlertRuleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'fromAirport'=>new AirportResource($this->whenLoaded('fromAirport',function (){
                return $this->fromAirport;
            })),
            'toAirport'=>new AirportResource($this->whenLoaded('toAirport',function (){
                return $this->toAirport;
            })),
            "adultsCount" => $this->adults_count,
            "childrenCount" => $this->children_count,
            "infantsCount" => $this->infants_count,
            "isOneWay" => $this->is_one_way,
            "classType" => $this->class_type,
            "__typename" => "Tickets_Match_Alert_Rules"
        ];
    }
}
