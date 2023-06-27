<?php

namespace App\Enums;
enum PushCategory
{
    const CHAT_MESSAGE = 'chat_message';
    const TICKET_RATING = 'ticket_rating';
    const TICKET_RATING_BACK = 'ticket_rating_back';
    const ALERT_TICKET = 'alert_ticket';
    const PROMOTE_MY_TICKET = 'promote_my_ticket';
    const FAVORITE_TICKET_REMOVED = 'favorite_ticket_removed';
    const NEW_PURCHASE = 'new_purchase';
}
