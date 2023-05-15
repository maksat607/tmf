<?php

namespace App\Services;

use App\Enums\TicketSortType;
use App\Filters\TicketFilter;
use App\Http\Requests\Ticket\StoreTicket;
use App\Models\TicketAirplaneTicket;
use App\Models\TicketBaseTicket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketService
{
    /**
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function index(Request $request): array|\Illuminate\Database\Eloquent\Collection
    {

        $validatedData = $request->validate([
            'offset' => 'sometimes|integer',
            'limit' => 'sometimes|integer',
        ]);

        $filter = new TicketFilter();
        $query = TicketBaseTicket::with([
            'user',
            'departureAirport',
            'arrivalAirport',
            'currency',
            'favorite',
            'ticketAirplaneTicket',
            'ticketAirplaneTicket.fromAirport',
            'ticketAirplaneTicket.toAirport',
            'ticketAirplaneTicket.returnFromAirport',
            'ticketAirplaneTicket.returnToAirport',
            'ticketAirplaneTicket.airline'
        ]);
        $items = $filter->setOffset($request->get('offset',0))
            ->setLimit($request->get('limit',200))
            ->setFromAirport($request->get('from_airport'))
            ->setToAirport($request->get('to_airport'))
            ->setFromStartDateAt(($request->get('fromStartDateAt')))
            ->setToStartDateAt(($request->get('toStartDateAt')))
            ->setIsOnlyWithReturnWay($request->get('isOnlyWithReturnWay'))
            ->setIsOnlyWithoutReturnWay($request->get('isOnlyWithoutReturnWay'))
            ->setClassType($request->get('classType'))
            ->setAdultsCount($request->get('adultsCount'))
            ->setChildrenCount($request->get('childrenCount'))
            ->setInfantsCount($request->get('infantsCount'))
//            ->setWatcher($request->user())
            ->setSortType($request->get('sortType') ?? TicketSortType::TOP_POSITION)
            ->apply($query);

        return $items;
    }

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
                'created_at' => now(),
                'currency_id' => $request->currency,
                'location_latitude' => $request->locationLatitude,
                'location_longitude' => $request->locationLongitude,
                'location_name' => $request->locationName,
                'price' => $request->price,
                'price_with_commission' => (int)($request->price + $request->price * ((int)SettingsService::getSetting('commission')) / 100),
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
}
