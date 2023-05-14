<?php
namespace App\Http\Controllers\Ticket;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\Ticket\StoreTicket;
use App\Http\Resources\Ticket\TicketResource;
use App\Models\TicketBaseTicket;
use App\Services\TicketService;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    private $ticketService;
    private $cachePrefix = 'tickets_';

    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
        $this->middleware('auth.access_token')->only(['store', 'update', 'show', 'upTopPosition', 'sold', 'destroy']);
    }

    private function generateCacheKey($suffix)
    {
        return $this->cachePrefix . $suffix;
    }

    public function index(Request $request)
    {
        $cacheKey = $this->generateCacheKey('index_' . $request->getQueryString());
        $cacheDuration = 3600; // Cache duration in seconds (1 hour)

        $response = Cache::remember($cacheKey, $cacheDuration, function () use ($request, $cacheKey) {
            $items = $this->ticketService->index($request);
            $count = $items->count();
            $tickets = TicketResource::collection($items);

            return Response::json($tickets)->header('X-Total-Count', $cacheKey);
        });

        return $response;
    }

    public function store(StoreTicket $request)
    {
        $baseTicket = $this->ticketService->store($request);
        Cache::forget($this->generateCacheKey('index_*'));

        return new TicketResource($baseTicket);
    }

    public function update(StoreTicket $request, string $id)
    {
        $baseTicket = $this->ticketService->update($id, $request);
        Cache::forget($this->generateCacheKey('index_*'));

        return new TicketResource($baseTicket);
    }

    public function destroy(string $id)
    {
        $this->ticketService->destroy($id);
        Cache::forget($this->generateCacheKey('index_*'));

        return response()->noContent();
    }

    public function upTopPosition(string $id)
    {
        $baseTicket = $this->ticketService->upTopPosition($id);

        // Invalidate the index cache
        Cache::forget($this->generateCacheKey('index_*'));

        return new TicketResource($baseTicket);
    }

    public function mylist(Request $request)
    {
        $tickets = auth()->user()->tickets()->with([
            'user',
            'departureAirport',
            'arrivalAirport',
            'currency',
            'ticketAirplaneTicket.fromAirport',
            'ticketAirplaneTicket.toAirport',
            'ticketAirplaneTicket.returnFromAirport',
            'ticketAirplaneTicket.returnToAirport',
            'ticketAirplaneTicket.airline'
        ])->get();

        return TicketResource::collection($tickets);
    }

    public function sold(Request $request, TicketBaseTicket $ticket)
    {
        $user =

            auth()->user();
        if ($user->id !== $ticket->user_id) {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        $ticket->is_sold = 1;
        $ticket->save();
        return response()->noContent();
    }


}
