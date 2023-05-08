<?php

namespace App\Services;

use App\Http\Requests\Ticket\StoreTicket;
use App\Http\Resources\Ticket\TicketResource;
use App\Models\AuthUser;
use App\Models\TicketAirplaneTicket;
use App\Models\TicketBaseTicket;
use App\Services\TicketFilterService;
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
        $user = auth()->user();
        DB::beginTransaction();
        try {
            $ticketData = [
                'user_id' => $user->id,
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
                'start_date_at' => Carbon::parse($request->startDateAt)->format('Y-m-d H:i:s'),
                'end_date_at' => Carbon::parse($request->endDateAt)->format('Y-m-d H:i:s'),
                'end_date_at' => Carbon::parse($request->endDateAt)->format('Y-m-d H:i:s'),
                'return_start_date_at' => Carbon::parse($request->returnStartDateAt)->format('Y-m-d H:i:s'),
                'return_end_date_at' => Carbon::parse($request->returnEndDateAt)->format('Y-m-d H:i:s'),
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
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        return $baseTicket->load(['user', 'ticketAirplaneTicket.airline',
            'departureAirport',
            'arrivalAirport',
            'ticketAirplaneTicket.fromAirport',
            'ticketAirplaneTicket.toAirport',
            'ticketAirplaneTicket.returnFromAirport',
            'ticketAirplaneTicket.returnToAirport'
        ]);

    }

    public function getUser(int $id): AuthUser
    {
        $user = AuthUser::find($id);

        if (!$user) {
            throw new \Exception('User not found');
        }

        return $user;
    }
}
