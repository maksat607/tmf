<?php

namespace App\Services;

use App\Entity\Auth\User;
use App\Entity\Chat\Message;
use App\Entity\Ticket\AirplaneTicket;
use App\Enums\PushCategory;
use App\Helpers\OneSignalClient;
use App\Models\AuthUser;
use App\Models\Push;
use App\Models\TicketAirplaneTicket;

class PushService
{
    private $oneSignalClient;

    public function __construct(string $oneSignalBaseUrl, string $oneSignalAppId, string $oneSignalApiKey)
    {
        $this->oneSignalClient = new OneSignalClient($oneSignalBaseUrl, $oneSignalAppId, $oneSignalApiKey);
    }

    public function sendChatMessage(ChatMessage $message)
    {
        $chat = $message->chat;
        $fromUser = $message->user;
        $toUser = $fromUser->id ===  $chat->replyUser->id ? $chat->ticketUser : $chat->replyUser;


        if (!$toUser->one_signal_identifier) {
            return;
        }

        if ($message->files()->count()>0) {
            $title = sprintf('%s sent you an image', $fromUser->name);
            $subtitle = '';
            $text = '';
        } else {
            $title = 'You have a new message';
            $subtitle = $fromUser->name;
            $text = $message->text;
        }

        $push = new Push($title, PushCategory::CHAT_MESSAGE);
        $push->setOneSignalPlayerId($toUser->one_signal_identifier);
        $push->setSubtitle($subtitle);
        $push->setText($text);
        $push->setParameters([
            'chatId' => $chat->id
        ]);

        $this->oneSignalClient->sendPush($push);
    }


    public function sendUserRating(AuthUser $destination, AuthUser $author, bool $isIncrease, bool $isBack)
    {
        if (!$destination->one_signal_identifier) {
            return;
        }

        $title = sprintf('%s just rated experience with you', $author->name);
        if ($isBack) {
            $text = sprintf('your rating now is 😀 %d 🙁 %d', $destination->increases_count, $destination->decreases_count);
            $category = PushCategory::TICKET_RATING_BACK;
        } else {
            $text = 'tap here to rate him(her) back';
            $category = PushCategory::TICKET_RATING;
        }

        $push = new Push($title, $category);
        $push->setOneSignalPlayerId($destination->one_signal_identifier);
        $push->setText($text);
        $push->setParameters([
            'userId' => $author->id,
            'isLike' => $isIncrease
        ]);

        $this->oneSignalClient->sendPush($push);
    }



    public function sendAirplaneTicketMatched(TicketAirplaneTicket $airplaneTicket, AuthUser $user)
    {
        if (!$user->one_signal_identifier) {
            return;
        }

        if ($airplaneTicket->is_price_dropped) {
            $title = sprintf(
                '%s -> %s. Price dropped',
                $airplaneTicket->fromAirport->code,
                $airplaneTicket->toAirport->code
            );

        } else {
            $title = sprintf(
                '%s -> %s. New ticket was just added',
                $airplaneTicket->fromAirport->code,
                $airplaneTicket->toAirport->code
            );
        }

        $peopleCount = $airplaneTicket->adults_count + $airplaneTicket->children_count + $airplaneTicket->infants_count;

        $text = sprintf('%s. %s%d %d %s',
            $airplaneTicket->start_date_at->format('d M Y'),
            $airplaneTicket->ticketBaseTicket->code,
            $airplaneTicket->ticketBaseTicket->price,
            $peopleCount,
            $peopleCount === 1 && $airplaneTicket->getAdultsCount() === 1 ? 'adult' : 'people'
        );

        $push = new Push($title, PushCategory::ALERT_TICKET);
        $push->setOneSignalPlayerId($user->one_signal_identifier);
        $push->setText($text);
        $push->setParameters([
            'startDateAt' => $airplaneTicket->start_date_at->format(DATE_ATOM),
            'ticketId' => $airplaneTicket->id,
            'fromAirportCode' => $airplaneTicket->fromAirport->code,
            'toAirportCode' => $airplaneTicket->toAirport->code,
            'passengersCount' => $airplaneTicket->passengersCount(),
            'classType' => $airplaneTicket->class_type,
            'formattedPrice' => sprintf('%s%d', $airplaneTicket->ticketBaseTicket->currency->symbol, $airplaneTicket->ticketBaseTicket->price)
        ]);

        $this->oneSignalClient->sendPush($push);
    }


    public function sendBumpUpAirplaneTicket(TicketAirplaneTicket $airplaneTicket, AuthUser $user)
    {
        if (!$user->one_signal_identifier) {
            return;
        }

        $title = 'Bump up your listing';
        $text = 'Increase your chance to sell the ticket';

        $push = new Push($title, PushCategory::PROMOTE_MY_TICKET);
        $push
            ->setOneSignalPlayerId($user->one_signal_identifier)
            ->setText($text)
            ->setParameters([
                'startDateAt' => $airplaneTicket->start_date_at->format(DATE_ATOM),
                'ticketId' => $airplaneTicket->id,
                'fromAirportCode' => $airplaneTicket->fromAirport->code,
                'toAirportCode' => $airplaneTicket->toAirport->code,
                'passengersCount' => $airplaneTicket->passengersCount(),
                'classType' => $airplaneTicket->class_type,
                'formattedPrice' => sprintf('%s%d', $airplaneTicket->ticketBaseTicket->currency->symbol, $airplaneTicket->ticketBaseTicket->price)
            ]);

        $this->oneSignalClient->sendPush($push);
    }




    public function sendTicketRemovedFromFavorite(TicketAirplaneTicket $airplaneTicket, AuthUser $user): void
    {
        if (!$user->one_signal_identifier) {
            return;
        }

        $title = 'Someone bought a ticket you saved';
        $text = sprintf(
            'Ticket %s->%s you added to your favorites was purchased',
            $airplaneTicket->fromAirport->code,
            $airplaneTicket->toAirport->code
        );

        $push = new Push($title, PushCategory::FAVORITE_TICKET_REMOVED);
        $push
            ->setOneSignalPlayerId($user->one_signal_identifier)
            ->setText($text)
            ->setParameters([
                'ticketId' => $airplaneTicket->id,
                'fromAirportCode' => $airplaneTicket->fromAirport->code,
                'toAirportCode' => $airplaneTicket->toAirport->code,
            ]);

        $this->oneSignalClient->sendPush($push);
    }



    public function sendNewPurchase(TicketAirplaneTicket $airplaneTicket, ?string $buyerName): void
    {
        $user = $airplaneTicket->user;
        if (!$user->one_signal_identifier) {
            return;
        }

        $title = sprintf('%s wants to buy your ticket', $buyerName ? $buyerName : 'Someone');
        $text = 'You have 48 hours to transfer the ticket';

        $push = new Push($title, PushCategory::NEW_PURCHASE);
        $push
            ->setOneSignalPlayerId($user->one_signal_identifier)
            ->setText($text)
            ->setParameters([
                'ticketId' => $airplaneTicket->id,
            ]);

        $this->oneSignalClient->sendPush($push);
    }



    public function sendPushByTemplateId(AuthUser $user, string $id)
    {
        if ($user->one_signal_identifier) {
            $this->oneSignalClient->sendPushByTemplateId($user->one_signal_identifier, $id);
        }
    }

}
