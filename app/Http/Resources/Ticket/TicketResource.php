<?php

namespace App\Http\Resources\Ticket;

use App\Http\Resources\Airport\AirportResource;
use App\Http\Resources\Auth\AuthUserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
//        return parent::toArray($request);
        return [
            "id" => $this->id,
            "type" => "airplane",
            "author" => new AuthUserResource($this->whenLoaded('user', function () {
                return $this->user;
            })),

            "createdAt" => $this->created_at,
            "locationLatitude" => $this->location_latitude,
            "locationLongitude" => $this->location_longitude,
            "locationName" => $this->location_name,
            "price" => $this->price,
            "previousPrice" => $this->previous_price,
            "discountType" => $this->discount_type,
            "currency" => $this->whenLoaded('currency', function () {
                return $this->currency;
            }),
            "fromAirport" =>new AirportResource($this->whenLoaded('ticketAirplaneTicket',function (){
                return $this->ticketAirplaneTicket->fromAirport;
            })),
            "toAirport" => new AirportResource($this->whenLoaded('ticketAirplaneTicket', function () {
                return $this->ticketAirplaneTicket->toAirport;
            })),
            "returnFromAirport" => new AirportResource($this->whenLoaded('ticketAirplaneTicket', function () {
                return $this->ticketAirplaneTicket->returnFromAirport;
            })),
            "returnToAirport" => new AirportResource($this->whenLoaded('ticketAirplaneTicket', function () {
                return $this->ticketAirplaneTicket->returnToAirport;
            })),
            "isOneWay" => $this->ticketAirplaneTicket->is_one_way,
            "startDateAt" => $this->ticketAirplaneTicket->start_date_at,
            "endDateAt" => $this->ticketAirplaneTicket->end_date_at,
            "returnStartDateAt" => $this->ticketAirplaneTicket->return_start_date_at,
            "returnEndDateAt" => $this->ticketAirplaneTicket->return_end_date_at,
            "stopsCount" => $this->ticketAirplaneTicket->stops_count,
            "returnStopsCount" => $this->ticketAirplaneTicket->return_stops_count,
            "classType" => $this->ticketAirplaneTicket->class_type,
            "adultsCount" => $this->ticketAirplaneTicket->adults_count,
            "childrenCount" => $this->ticketAirplaneTicket->children_count,
            "infantsCount" => $this->ticketAirplaneTicket->infants_count,
            "airline" => $this->airline,
            "isSold" => $this->is_sold,
            "topPositionExpiredAt" => $this->top_position_expired_at,
            "isHighlighted" => $this->is_highlighted,
            "__typename" => "Ticket_AirplaneTicket",
            "purchase" => $this->purchases

        ];
    }
}
