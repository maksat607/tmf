<?php

namespace App\Filters;

use App\Enums\TicketSortType;
use App\Models\DictionaryAirport;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

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

    public function setFromAirport(DictionaryAirport $fromAirport = null)
    {
        $this->fromAirport = $fromAirport;
        return $this;
    }

    public function setToAirport(DictionaryAirport $toAirport = null)
    {
        $this->toAirport = $toAirport;
        return $this;
    }

    public function setFromStartDateAt(Carbon $fromStartDateAt = null)
    {
        $this->fromStartDateAt = $fromStartDateAt;
        return $this;
    }

    public function setToStartDateAt(Carbon $toStartDateAt = null)
    {
        $this->toStartDateAt = $toStartDateAt;
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
        $this->sortType = $sortType;
        return $this;
    }

    public function apply(Builder $builder)
    {
        $builder->skip($this->offset)->take($this->limit);

        if ($this->fromAirport) {
            $builder->where('departure_airport_code', $this->fromAirport->code);
        }
        if ($this->toAirport) {
            $builder->where('arrival_airport_code', $this->toAirport->code);
        }
        if ($this->fromStartDateAt) {
            $builder->where('start_date', '>=', $this->fromStartDateAt);
        }
        if ($this->toStartDateAt) {
            $builder->where('start_date', '<=', $this->toStartDateAt);
        }
        if ($this->isOnlyWithReturnWay) {
            $builder->whereNotNull('return_date');
        }
        if ($this->isOnlyWithoutReturnWay) {
            $builder->whereNull('return_date');
        }

        if ($this->classType) {
            $builder->where('class', $this->classType);
        }


        if ($this->adultsCount) {
            $builder->whereHas('ticketAirplaneTicket', function ($query) {
                $query->where('adults_count', $this->adultsCount);
            });
        }


        if ($this->childrenCount) {
            $builder->where('children_count', $this->childrenCount);
        }
        if ($this->infantsCount) {
            $builder->where('infants_count', $this->infantsCount);
        }

        switch ($this->sortType) {
            case TicketSortType::PRICE_ASC:
                $builder->orderBy('price', 'asc');
                break;
            case TicketSortType::PRICE_DESC:
                $builder->orderBy('price', 'desc');
                break;
            case TicketSortType::DATE_ASC:
                $builder->orderBy('start_date', 'asc');
                break;
            case TicketSortType::DATE_DESC:
                $builder->orderBy('start_date', 'desc');
                break;
            default:
                $builder->orderBy('price', 'asc');
                break;
        }

        if ($this->watcher) {
            $builder->where('watcher_id', $this->watcher->id);
        }

        return $builder;
    }

//    public function filter(Builder $builder)
//    {
//        $builder->when($this->fromAirport, function ($query, $fromAirport) {
//            return $query->where('from_airport', $fromAirport);
//        })
//            ->when($this->toAirport, function ($query, $toAirport) {
//                return $query->where('to_airport', $toAirport);
//            })
//            ->when($this->fromStartDateAt, function ($query, $fromStartDateAt) {
//                return $query->where('start_date', '>=', $fromStartDateAt);
//            })
//            ->when($this->toStartDateAt, function ($query, $toStartDateAt) {
//                return $query->where('start_date', '<=', $toStartDateAt);
//            })
//            ->when($this->isOnlyWithReturnWay, function ($query) {
//                return $query->where('has_return_way', true);
//            })
//            ->when($this->isOnlyWithoutReturnWay, function ($query) {
//                return $query->where('has_return_way', false);
//            })
//            ->when($this->classType, function ($query, $classType) {
//                return $query->where('class_type', $classType);
//            })
//            ->when($this->adultsCount, function ($query, $adultsCount) {
//                return $query->where('adults_count', $adultsCount);
//            })
//            ->when($this->childrenCount, function ($query, $childrenCount) {
//                return $query->where('children_count', $childrenCount);
//            })
//            ->when($this->infantsCount, function ($query, $infantsCount) {
//                return $query->where('infants_count', $infantsCount);
//            })
//            ->when($this->watcher, function ($query, $watcher) {
//                return $query->where('user_id', $watcher->id);
//            })
//            ->orderBy($this->sortType);
//
//        return $builder;
//    }
}

