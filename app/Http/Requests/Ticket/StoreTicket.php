<?php

namespace App\Http\Requests\Ticket;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicket extends FormRequest
{
    public function all($keys = null)
    {
        $data = parent::all($keys);
        if(isset($data['discountType'])&&$data['discountType']=='usual'){
            $data['topPositionExpiredAt'] = now();
        }
        if(isset($data['discountType'])&&$data['discountType']=='urgent'){
            $data['topPositionExpiredAt'] = now()->addDays(3);
        }
        if(isset($data['discountType'])&&$data['discountType']=='promo'){
            $data['topPositionExpiredAt'] = now()->addDays(7);
        }
        if(isset($data['discountType'])&&$data['discountType']=='discount'){
            $data['topPositionExpiredAt'] = now();
        }
        return $data;
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'fromAirport' => 'sometimes|integer',
            'toAirport' => 'sometimes|integer',
            'returnFromAirport' => 'sometimes|integer',
            'returnToAirport' => 'sometimes|integer',
            'isOneWay' => 'sometimes|boolean',
            'startDateAt' => 'sometimes|date_format:Y-m-d\TH:i:s\Z',
            'endDateAt' => 'sometimes|date_format:Y-m-d\TH:i:s\Z',
            'returnStartDateAt' => 'sometimes|date_format:Y-m-d\TH:i:s\Z',
            'returnEndDateAt' => 'sometimes|date_format:Y-m-d\TH:i:s\Z',
            'stopsCount' => 'sometimes|integer',
            'returnStopsCount' => 'sometimes|integer',
            'classType' => 'sometimes|in:economy,business,first',
            'adultsCount' => 'sometimes|integer|min:1',
            'childrenCount' => 'sometimes|integer|min:0',
            'infantsCount' => 'sometimes|integer|min:0',
            'airline' => 'sometimes|integer',
            'locationLatitude' => 'sometimes|numeric',
            'locationLongitude' => 'sometimes|numeric',
            'locationName' => 'sometimes|string|max:255',
            'price' => 'sometimes|integer|min:0',
            'previousPrice' => 'sometimes|integer|min:0',
            'discountType' => 'sometimes|in:usual,promo,discounted,urgent',
            'currency' => 'sometimes|integer',
            'isHighlighted' => 'sometimes|boolean',
        ];
    }
}
