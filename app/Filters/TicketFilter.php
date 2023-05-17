<?php

namespace App\Filters;

use App\Enums\TicketSortType;
use App\Services\SettingsService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class TicketFilter
{
    protected $offset;
    protected $limit;
    protected $fromAirport;
    protected $toAirport;
    protected $fromStartDateAt;
    protected $toStartDateAt;
    protected $isOnlyWithReturnWay;
    protected $isOnlyWithoutReturnWay;
    protected $classType;
    protected $adultsCount;
    protected $childrenCount;
    protected $infantsCount;
    protected $watcher;
    protected $sortType;

    public function setOffset(int $offset)
    {

        $this->offset = $offset;
        return $this;
    }

    public function setLimit(int $limit)
    {
        $this->limit = $limit;
        return $this;
    }

    public function setFromAirport($fromAirport)
    {
        $this->fromAirport = $fromAirport;
        return $this;
    }

    public function setToAirport($toAirport)
    {
        $this->toAirport = $toAirport;
        return $this;
    }

    public function setFromStartDateAt($fromStartDateAt)
    {
        if ($fromStartDateAt) {
            $this->fromStartDateAt = Carbon::parse($fromStartDateAt);
        }
        return $this;
    }

    public function setToStartDateAt($toStartDateAt)
    {
        if ($toStartDateAt) {
            $this->toStartDateAt = Carbon::parse($toStartDateAt);
        }
        return $this;
    }

    public function setIsOnlyWithReturnWay($isOnlyWithReturnWay)
    {
        $this->isOnlyWithReturnWay = $isOnlyWithReturnWay;
        return $this;
    }

    public function setIsOnlyWithoutReturnWay($isOnlyWithoutReturnWay)
    {
        $this->isOnlyWithoutReturnWay = $isOnlyWithoutReturnWay;
        return $this;
    }

    public function setClassType(string $classType = null)
    {
        $this->classType = $classType;
        return $this;
    }

    public function setAdultsCount(int $adultsCount = null)
    {
        $this->adultsCount = $adultsCount;
        return $this;
    }

    public function setChildrenCount(int $childrenCount = null)
    {
        $this->childrenCount = $childrenCount;
        return $this;
    }

    public function setInfantsCount(int $infantsCount = null)
    {
        $this->infantsCount = $infantsCount;
        return $this;
    }

    public function setWatcher($watcher = null)
    {
        $this->watcher = $watcher;
        return $this;
    }

    public function setSortType(string $sortType = null)
    {
        if ($sortType === null) {
            $this->sortType = TicketSortType::TOP_POSITION;
        } else {
            $this->sortType = $sortType;
        }

        return $this;
    }

    public function apply(Builder $builder)
    {
        $builder->skip($this->offset)->take($this->limit);

        if ($this->fromAirport) {
            $builder->whereHas('ticketAirplaneTicket', function ($query) {
                $query->where('from_airport_id', $this->fromAirport);
            });
        }
        if ($this->toAirport) {
            $builder->whereHas('ticketAirplaneTicket', function ($query) {
                $query->where('to_airport_id', $this->toAirport);
            });
        }
        if ($this->fromStartDateAt) {
            $builder->whereHas('ticketAirplaneTicket', function ($query) {
                $query->where('start_date_at', '>=', $this->fromStartDateAt);
            });
        }
        if ($this->toStartDateAt) {
            $builder->whereHas('ticketAirplaneTicket', function ($query) {
                $query->where('start_date_at', '<=', $this->toStartDateAt);
            });
        }
        if ($this->isOnlyWithReturnWay) {
            $builder->whereHas('ticketAirplaneTicket', function ($query) {
                $query->where('is_one_way', '1');
            });
        }
        if ($this->isOnlyWithoutReturnWay) {
            $builder->whereHas('ticketAirplaneTicket', function ($query) {
                $query->where('is_one_way', '!=', '1');
            });
        }

        if ($this->classType) {
            $builder->whereHas('ticketAirplaneTicket', function ($query) {
                $query->where('class_type', $this->classType);
            });
        }


        if ($this->adultsCount) {
            $builder->whereHas('ticketAirplaneTicket', function ($query) {
                $query->where('adults_count', $this->adultsCount);
            });
        }

        if ($this->childrenCount) {
            $builder->whereHas('ticketAirplaneTicket', function ($query) {
                $query->where('children_count', $this->childrenCount);
            });
        }
        if ($this->infantsCount) {
            $builder->whereHas('ticketAirplaneTicket', function ($query) {
                $query->where('infants_count', $this->infantsCount);
            });
        }
        if ($this->watcher) {
            $builder->where('watcher_id', $this->watcher->id);
        }
        $builder->whereHas('ticketAirplaneTicket', function ($query) {
            $query->where('start_date_at', '>', now()->addHours((int)SettingsService::getSetting('time_for_ticket_publication')));
        });
        return $this->applyOrders($builder);

    }

    /**
     * @param Builder $builder
     */
    public function applyOrders(Builder $builder)
    {

        $now = now();
        switch ($this->sortType) {
            case TicketSortType::DEPARTURE:
                $query = $builder->join('ticket__airplane_tickets', 'ticket__base_tickets.id', '=', 'ticket__airplane_tickets.id')
                    ->where('ticket__airplane_tickets.start_date_at', '>', $now)
                    ->orderByRaw("CASE
                        WHEN discount_type = 'promo' AND top_position_expired_at > '$now' THEN 0
                        ELSE 1
                          END")
                    ->orderByRaw("CASE
                        WHEN discount_type = 'promo' AND top_position_expired_at > '$now' THEN top_position_expired_at
                        ELSE NULL
                         END ASC")
//                    ->orderByRaw("CASE
//                        WHEN top_position_expired_at > '$now' THEN top_position_expired_at
//                        ELSE NULL
//                         END DESC")
                    ->orderBy('ticket__airplane_tickets.start_date_at', 'ASC')
                    ->get();
                break;
            default:
                $query = $builder->orderByRaw("CASE WHEN top_position_expired_at > '$now' THEN 0 ELSE 1 END")
                    ->orderBy('created_at', 'desc')
                    ->get();
                break;

        }
        return $query;
    }
}

