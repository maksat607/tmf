<?php
namespace  App\Traits;
use App\Models\TicketAirplaneTicket;

trait ChatTitleResolverTrait
{
    private function resolveChatTitleByTicket(TicketAirplaneTicket $airplaneTicket): string
    {
        return sprintf(
            '%s -> %s. %s.',
            $airplaneTicket->fromAirport->code,
            $airplaneTicket->toAirport->code,
            $airplaneTicket->start_date_at->format('d M')
        );
    }
}
