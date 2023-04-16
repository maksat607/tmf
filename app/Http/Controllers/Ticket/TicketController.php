<?php

namespace App\Http\Controllers\Ticket;
use App\Enums\TicketSortType;
use App\Filters\TicketFilter;
use App\Http\Controllers\Controller;
use App\Http\Resources\Ticket\TicketResource;
use App\Models\TicketBaseTicket;
use Illuminate\Http\Request;
class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
//        return $request->all();
        $filter = new TicketFilter();

        $items = $filter->setOffset($request->get('offset'))
            ->setLimit($request->get('limit'))
            ->setFromAirport($request->get('from_airport'))
            ->setToAirport($request->get('to_airport'))
            ->setFromStartDateAt($request->get('from_start_date_at'))
            ->setToStartDateAt($request->get('to_start_date_at'))
            ->setIsOnlyWithReturnWay($request->get('is_only_with_return_way'))
            ->setIsOnlyWithoutReturnWay($request->get('is_only_without_return_way'))
            ->setClassType($request->get('class_type'))
            ->setAdultsCount($request->get('adults_count'))
            ->setChildrenCount($request->get('children_count'))
            ->setInfantsCount($request->get('infants_count'))
            ->setWatcher($request->user())
            ->setSortType($request->get('sort_type') ?: TicketSortType::TOP_POSITION)
            ->apply(TicketBaseTicket::with(['user','airline',
                'departureAirport',
                'arrivalAirport', 'purchases',
                'ticketAirplaneTicket.fromAirport',
                'ticketAirplaneTicket.toAirport',
                'ticketAirplaneTicket.returnFromAirport',
                'ticketAirplaneTicket.returnToAirport'
            ]))
            ->get()
            ;

            $count = $items->count();


        return response()->json([

           TicketResource::collection($items),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function destroy(string $id)
    {
        //
    }
}
