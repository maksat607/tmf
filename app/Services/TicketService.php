<?php

namespace App\Services;

use App\Http\Requests\Ticket\StoreTicket;
use App\Models\TicketAirplaneTicket;
use App\Models\TicketBaseTicket;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TicketService
{


    /**
     *
     *
     * Store a newly created resource in storage.
     *
     *
     */
    public function store(StoreTicket $request)
    {
        return $this->transactionWrapper(function () use ($request) {
            $ticketData = [
                'user_id' => auth()->user()->id,
                'currency_id' => $request->currency,
                'location_latitude' => $request->locationLatitude,
                'location_longitude' => $request->locationLongitude,
                'location_name' => $request->locationName,
                'price' => $request->price,
                'previous_price' => $request->previousPrice,
                'discount_type' => $request->discountType,
                'is_sold' => false,
                'expired_at' => null,
                'discriminator' => 'airplaneticket',
                'top_position_expired_at' => $request->topPositionExpiredAt,
                'is_highlighted' => $request->isHighlighted,
                'is_bump_up_notification_sent' => false,
                'status' => 0,
            ];

            $baseTicket = new TicketBaseTicket($ticketData);
            $baseTicket->save();

            $airplaneTicketData = [
                'from_airport_id' => $request->fromAirport,
                'to_airport_id' => $request->toAirport,
                'return_from_airport_id' => $request->returnFromAirport,
                'return_to_airport_id' => $request->returnToAirport,
                'airline_id' => $request->airline,
                'is_one_way' => $request->isOneWay,
                'start_date_at' => $request->startDateAt ? Carbon::parse($request->startDateAt)->format('Y-m-d H:i:s') : null,
                'end_date_at' => $request->endDateAt ? Carbon::parse($request->endDateAt)->format('Y-m-d H:i:s') : null,
                'return_start_date_at' => $request->returnStartDateAt ? Carbon::parse($request->returnStartDateAt)->format('Y-m-d H:i:s') : null,
                'return_end_date_at' => $request->returnEndDateAt ? Carbon::parse($request->returnEndDateAt)->format('Y-m-d H:i:s') : null,
                'stops_count' => $request->stopsCount,
                'return_stops_count' => $request->returnStopsCount,
                'class_type' => $request->classType,
                'adults_count' => $request->adultsCount,
                'children_count' => $request->childrenCount,
                'infants_count' => $request->infantsCount,
                'is_processed_match_alert' => false,
                'is_price_dropped' => false,
            ];
            $airplaneTicket = new TicketAirplaneTicket($airplaneTicketData);
            $airplaneTicket->ticketBaseTicket()->associate($baseTicket);
            $airplaneTicket->save();

            return $this->fullTicketInfo($baseTicket);
        });
    }



    public function fullTicketInfo(TicketBaseTicket $ticket): TicketBaseTicket
    {
        return $ticket->load(['user',
            'currency',
            'ticketAirplaneTicket.airline',
            'departureAirport',
            'arrivalAirport',
            'ticketAirplaneTicket.fromAirport',
            'ticketAirplaneTicket.toAirport',
            'ticketAirplaneTicket.returnFromAirport',
            'ticketAirplaneTicket.returnToAirport'
        ]);
    }

    public function update(string $id, StoreTicket $request)
    {
        return $this->transactionWrapper(function () use ($id, $request) {
            $baseTicket = TicketBaseTicket::findOrFail($id);
            if (auth()->user()->id !== $baseTicket->user_id) {
                return response()->json(['error' => 'Forbidden'], 403);
            }

            $ticketData = [
                'currency_id' => $request->currency,
                'location_latitude' => $request->locationLatitude,
                'location_longitude' => $request->locationLongitude,
                'location_name' => $request->locationName,
                'price' => $request->price,
                'previous_price' => $request->previousPrice,
                'discount_type' => $request->discountType,
                'is_sold' => $request->isSold,
                'expired_at' => $request->expiredAt,
                'top_position_expired_at' => $request->topPositionExpiredAt,
                'is_highlighted' => $request->isHighlighted,
                'is_bump_up_notification_sent' => $request->isBumpUpNotificationSent,
                'status' => $request->status,
            ];

            $baseTicket->update($ticketData);

            $airplaneTicket = $baseTicket->ticketAirplaneTicket;

            $airplaneTicketData = [
                'from_airport_id' => $request->fromAirport,
                'to_airport_id' => $request->toAirport,
                'return_from_airport_id' => $request->returnFromAirport,
                'return_to_airport_id' => $request->returnToAirport,
                'airline_id' => $request->airline,
                'is_one_way' => $request->isOneWay,
                'start_date_at' => Carbon::parse($request->startDateAt)->format('Y-m-d H:i:s'),
                'end_date_at' => Carbon::parse($request->endDateAt)->format('Y-m-d H:i:s'),
                'return_start_date_at' => Carbon::parse($request->returnStartDateAt)->format('Y-m-d H:i:s'),
                'return_end_date_at' => Carbon::parse($request->returnEndDateAt)->format('Y-m-d H:i:s'),
                'stops_count' => $request->stopsCount,
                'return_stops_count' => $request->returnStopsCount,
                'class_type' => $request->classType,
                'adults_count' => $request->adultsCount,
                'children_count' => $request->childrenCount,
                'infants_count' => $request->infantsCount,
                'is_processed_match_alert' => $request->isProcessedMatchAlert,
                'is_price_dropped' => $request->isPriceDropped,
            ];

            $airplaneTicket->update($airplaneTicketData);

            return $this->fullTicketInfo($baseTicket);
        });
    }

    public function show(string $id)
    {
        $baseTicket = TicketBaseTicket::findOrFail($id);
        return $this->fullTicketInfo($baseTicket);
    }
    public function destroy(string $id)
    {
        return $this->transactionWrapper(function () use ($id) {
            $baseTicket = TicketBaseTicket::findOrFail($id);
            if (auth()->user()->id !== $baseTicket->user_id) {
                return response()->json(['error' => 'Forbidden'], 403);
            }

            $airplaneTicket = $baseTicket->ticketAirplaneTicket;
            $airplaneTicket->delete();
            $baseTicket->delete();
        });

        return response()->noContent();
    }
    public function upTopPosition(string $id)
    {
        $baseTicket = TicketBaseTicket::findOrFail($id);
//        if ($user->id !== $baseTicket->user_id) {
//            return response()->json(['error' => 'Forbidden'], 403);
//        }
        $baseTicket->top_position_expired_at = Carbon::now();
        $baseTicket->save();
        return $this->fullTicketInfo($baseTicket);
    }


    private function transactionWrapper(callable $callback)
    {
        DB::beginTransaction();
        try {
            $result = $callback();
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
