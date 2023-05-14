<?php

namespace App\Enums;



enum TicketSortType
{
    const PRICE_ASC = 'price_asc';
    const PRICE_DESC = 'price_desc';
    const DATE_ASC = 'date_asc';
    const DATE_DESC = 'date_desc';
    const DEPARTURE = 'departure';
    const TOP_POSITION = 'top_position';
}
