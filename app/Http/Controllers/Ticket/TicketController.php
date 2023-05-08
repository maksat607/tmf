<?php

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ticket\StoreTicket;
use App\Http\Resources\Ticket\TicketResource;
use App\Models\TicketAirplaneTicket;
use App\Models\TicketBaseTicket;
use App\Services\TicketFilterService;
use App\Services\TicketService;
use Carbon\Carbon;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{

    private TicketFilterService $ticketStoreService;

    public function __construct(public TicketFilterService $ticketFilterService)
    {
        $this->middleware('auth.access_token')->only(['store', 'update', 'show','upTopPosition']);
        $this->ticketService = new TicketService();
    }

    /**
     *
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $items = $this->ticketFilterService->getFilteredTickets($request);
        $count = $items->count();
        return response()->json(TicketResource::collection($items))->header('X-Total-Count', $count);
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
        $baseTicket = $this->ticketService->store($request);
        return new TicketResource($baseTicket);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $baseTicket = TicketBaseTicket::findOrFail($id);
        $user = auth()->user();
//        if ($user->id !== $baseTicket->user_id) {
//            return response()->json(['error' => 'Unauthorized'], 401);
//        }
        return new TicketResource($baseTicket->load(['user', 'ticketAirplaneTicket.airline',
            'departureAirport',
            'arrivalAirport',
            'ticketAirplaneTicket.fromAirport',
            'ticketAirplaneTicket.toAirport',
            'ticketAirplaneTicket.returnFromAirport',
            'ticketAirplaneTicket.returnToAirport'
        ]));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreTicket $request, string $id)
    {
        $user = auth()->user();
        DB::beginTransaction();

        try {
            $baseTicket = TicketBaseTicket::findOrFail($id);

            // Check if the authenticated user is the owner of the ticket
            if ($user->id !== $baseTicket->user_id) {
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

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return new TicketResource($baseTicket->load(['user', 'ticketAirplaneTicket.airline',
            'departureAirport',
            'arrivalAirport',
            'ticketAirplaneTicket.fromAirport',
            'ticketAirplaneTicket.toAirport',
            'ticketAirplaneTicket.returnFromAirport',
            'ticketAirplaneTicket.returnToAirport'
        ]));
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = auth()->user();
        DB::beginTransaction();
        try {
            $baseTicket = TicketBaseTicket::findOrFail($id);
            if ($user->id !== $baseTicket->user_id) {
                return response()->json(['error' => 'Forbidden'], 403);
            }
            $airplaneTicket = $baseTicket->ticketAirplaneTicket;
            $airplaneTicket->delete();
            $baseTicket->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        return response()->noContent();
    }

    public function upTopPosition(string $id)
    {
        $user = auth()->user();
        $baseTicket = TicketBaseTicket::findOrFail($id);
//        if ($user->id !== $baseTicket->user_id) {
//            return response()->json(['error' => 'Forbidden'], 403);
//        }
        $baseTicket->top_position_expired_at = Carbon::now();
        $baseTicket->save();
        return new TicketResource($baseTicket->load(['user', 'ticketAirplaneTicket.airline',
            'departureAirport',
            'arrivalAirport',
            'ticketAirplaneTicket.fromAirport',
            'ticketAirplaneTicket.toAirport',
            'ticketAirplaneTicket.returnFromAirport',
            'ticketAirplaneTicket.returnToAirport'
        ]));
    }

    public function mylist(Request $request)
    {
        $tickets = auth()->user()->tickets()->with(['user',
            'departureAirport',
            'arrivalAirport',
            'currency',
//                'purchases',
            'ticketAirplaneTicket.fromAirport',
            'ticketAirplaneTicket.toAirport',
            'ticketAirplaneTicket.returnFromAirport',
            'ticketAirplaneTicket.returnToAirport',
            'ticketAirplaneTicket.airline'
        ])->get();
        return TicketResource::collection(
            $tickets
        );

    }

    public function sold(Request $request,TicketBaseTicket $ticket)
    {
        $user = auth()->user();
        if ($user->id !== $ticket->user_id) {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        $ticket->is_sold = 1;
        $ticket->save();
        return response()->noContent();
    }

}
