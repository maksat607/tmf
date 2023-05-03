<?php

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\Controller;
use App\Http\Resources\Ticket\TicketResource;
use App\Models\TicketBaseTicket;
use App\Models\TicketsFavoriteTicket;
use Illuminate\Http\Request;

class FavoriteTicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tickets = auth()->user()?->favoriteTickets;
        $ticketResources = collect();
        foreach ($tickets as $favoriteTicket) {
            $ticket = $favoriteTicket->ticket->load(['user',
                'departureAirport',
                'arrivalAirport',
                'currency',
//                'purchases',
                'ticketAirplaneTicket.fromAirport',
                'ticketAirplaneTicket.toAirport',
                'ticketAirplaneTicket.returnFromAirport',
                'ticketAirplaneTicket.returnToAirport',
                'ticketAirplaneTicket.airline'
            ]);
            $ticketResource = new TicketResource($ticket);
            $ticketResources->push($ticketResource);
        }
        return $ticketResources;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, TicketBaseTicket $ticket)
    {
        if (auth()->id() !== $ticket->user_id) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        if (!$ticket->favorite){
            $favoriteTicket = new TicketsFavoriteTicket();
            $favoriteTicket->ticket_id = $ticket->id;
            $favoriteTicket->user_id = auth()->id();
            $favoriteTicket->save();
        }

        return new TicketResource($ticket);
    }

    /**
     * Display the specified resource.
     */
    public function show(TicketBaseTicket $ticket)
    {
        if (auth()->id() !== $ticket->user_id) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $containsTicketId = auth()->user()?->favoriteTickets->contains(function ($favoriteTicket) use ($ticket) {
            return $favoriteTicket->ticket_id == $ticket->id;
        });
        if(!$containsTicketId){
            return response()->json([], 404)->header('Content-Type', 'application/json');
        }
        return response()->json(['id' => $ticket->favorite->id, "__typename" => "Tickets_Favorite_Ticket"]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TicketBaseTicket $ticket)
    {
        TicketsFavoriteTicket::where('user_id', auth()->id())->where('ticket_id', $ticket->id)->delete();
        return response()->noContent();
    }
}
