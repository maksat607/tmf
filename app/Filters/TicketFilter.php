<?php

namespace App\Filters;

use App\Enums\TicketSortType;
use App\Models\DictionaryAirport;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;


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

    public function setFromAirport( $fromAirport )
    {
        $this->fromAirport = $fromAirport;
        return $this;
    }

    public function setToAirport( $toAirport )
    {
        $this->toAirport = $toAirport;
        return $this;
    }

    public function setFromStartDateAt( $fromStartDateAt )
    {
        if ($fromStartDateAt){
            $this->fromStartDateAt = Carbon::parse($fromStartDateAt);
        }
        return $this;
    }

    public function setToStartDateAt( $toStartDateAt )
    {
        if ($toStartDateAt){
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
        if ($sortType == 'top_position') {
            $this->sortType = 'top_position_expired_at';
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
            $builder->where('ticketAirplaneTicket', function ($query) {
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
                $query->where('end_date_at', '>=', $this->toStartDateAt);
            });
        }
        if ($this->isOnlyWithReturnWay) {
            $builder->whereHas('ticketAirplaneTicket', function ($query) {
                $query->whereNotNull('is_one_way');
            });
        }
        if ($this->isOnlyWithoutReturnWay) {
            $builder->whereHas('ticketAirplaneTicket', function ($query) {
                $query->whereNull('is_one_way');
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

        switch ($this->sortType) {
            case TicketSortType::PRICE_ASC:
                $builder->orderBy('price', 'asc');
                break;
            case TicketSortType::PRICE_DESC:
                $builder->orderBy('price', 'desc');
                break;
            case TicketSortType::DATE_ASC:
                $builder->whereHas('ticketAirplaneTicket', function ($query) {
                    $query->orderBy('start_date_at', 'asc');
                });
                break;
            case TicketSortType::DATE_DESC:
                $builder->whereHas('ticketAirplaneTicket', function ($query) {
                    $query->orderBy('start_date_at', 'desc');
                });
                break;
            default:
                $builder->orderBy('price', 'asc');
                break;
        }

        if ($this->watcher) {
            $builder->where('watcher_id', $this->watcher->id);
        }
        $builder->whereHas('ticketAirplaneTicket', function ($query) {
            $query->where('start_date_at','>',now());
        });
        return $builder;
    }

}

