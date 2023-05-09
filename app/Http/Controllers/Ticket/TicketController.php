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
        $this->middleware('auth.access_token')->only(['store', 'update', 'show','upTopPosition','sold','destroy']);
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
        $baseTicket = $this->ticketService->show($id);
        return new TicketResource($baseTicket);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreTicket $request, string $id)
    {
        $baseTicket = $this->ticketService->update($id, $request);
        return new TicketResource($baseTicket);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        return $this->ticketService->destroy($id);

    }

    public function upTopPosition(string $id)
    {
        $baseTicket = $this->ticketService->upTopPosition($id);
        return new TicketResource($baseTicket);
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
