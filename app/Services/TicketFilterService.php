<?php

namespace App\Services;

use App\Enums\TicketSortType;
use App\Filters\TicketFilter;
use App\Models\TicketBaseTicket;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TicketFilterService
{
    /**

     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getFilteredTickets(Request $request): array|\Illuminate\Database\Eloquent\Collection
    {
        $filter = new TicketFilter();
        $items = $filter->setOffset($request->get('offset'))
            ->setLimit($request->get('limit'))
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
            ->setSortType($request->get('sort_type') ?: TicketSortType::TOP_POSITION)
            ->apply(TicketBaseTicket::with(['user',
                'departureAirport',
                'arrivalAirport',
                'currency',
//                'purchases',
                'ticketAirplaneTicket.fromAirport',
                'ticketAirplaneTicket.toAirport',
                'ticketAirplaneTicket.returnFromAirport',
                'ticketAirplaneTicket.returnToAirport',
                'ticketAirplaneTicket.airline'
            ]))
            ->get();
        return $items;
    }
}
