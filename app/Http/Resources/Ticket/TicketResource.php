<?php

namespace App\Http\Resources\Ticket;

use App\Http\Resources\Airline\AirlineResource;
use App\Http\Resources\Airport\AirportResource;
use App\Http\Resources\Auth\AuthUserResource;
use App\Http\Resources\Currency\CurrencyResource;
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
        return [
            "id" => $this->id,
            'isFavorite' => $this->favorite !== null,
            "created_at" => $this->created_at?->toIso8601String(),
            "discountType" => $this->discount_type,
            "topPositionExpiredAt" => $this->top_position_expired_at?->toIso8601String(),
            "startDateAt" => $this->ticketAirplaneTicket->start_date_at?->toIso8601String(),
            "type" => "airplane",
            "author" => new AuthUserResource($this->whenLoaded('user', function () {
                return $this->user;
            })),
            "locationLatitude" => $this->location_latitude,
            "locationLongitude" => $this->location_longitude,
            "locationName" => $this->location_name,
            "price" => (int)$this->price,
            "previousPrice" => (int)$this->previous_price,
            "priceWithCommission" => (int)($this->price_with_commission),

            "currency" => $this->whenLoaded('currency', function () {
                return new CurrencyResource($this->currency);
            }),
            "fromAirport" => new AirportResource($this->whenLoaded('ticketAirplaneTicket', function () {
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
            "airline" => $this->whenLoaded('ticketAirplaneTicket', function () {
                return new AirlineResource($this->ticketAirplaneTicket->airline);
            }),
            "isOneWay" => (boolean)$this->ticketAirplaneTicket->is_one_way,

            "endDateAt" => $this->ticketAirplaneTicket->end_date_at?->toIso8601String(),
            "returnStartDateAt" => $this->ticketAirplaneTicket->return_start_date_at?->toIso8601String(),
            "returnEndDateAt" => $this->ticketAirplaneTicket->return_end_date_at?->toIso8601String(),
            "stopsCount" => $this->ticketAirplaneTicket->stops_count,
            "returnStopsCount" => $this->ticketAirplaneTicket->return_stops_count,
            "classType" => $this->ticketAirplaneTicket->class_type,
            "adultsCount" => $this->ticketAirplaneTicket->adults_count,
            "childrenCount" => $this->ticketAirplaneTicket->children_count,
            "infantsCount" => $this->ticketAirplaneTicket->infants_count,
            "isSold" => (boolean)$this->is_sold,

            "isHighlighted" => (boolean)$this->is_highlighted,
            "__typename" => "Ticket_AirplaneTicket",
            "purchase" => $this->whenLoaded('purchases', function () {
                return $this->purchases;
            }),
        ];
    }
}
