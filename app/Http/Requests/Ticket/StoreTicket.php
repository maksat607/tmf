<?php

namespace App\Http\Requests\Ticket;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicket extends FormRequest
{
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
            'fromAirport' => 'required|integer',
            'toAirport' => 'required|integer',
            'returnFromAirport' => 'sometimes|integer',
            'returnToAirport' => 'sometimes|integer',
            'isOneWay' => 'required|boolean',
            'startDateAt' => 'sometimes|date_format:Y-m-d\TH:i:s\Z',
            'endDateAt' => 'sometimes|date_format:Y-m-d\TH:i:s\Z',
            'returnStartDateAt' => 'sometimes|date_format:Y-m-d\TH:i:s\Z',
            'returnEndDateAt' => 'sometimes|date_format:Y-m-d\TH:i:s\Z',
            'stopsCount' => 'required|integer',
            'returnStopsCount' => 'sometimes|integer',
            'classType' => 'required|in:economy,business,first',
            'adultsCount' => 'required|integer|min:1',
            'childrenCount' => 'sometimes|integer|min:0',
            'infantsCount' => 'sometimes|integer|min:0',
            'airline' => 'required|integer',
            'locationLatitude' => 'sometimes|numeric',
            'locationLongitude' => 'sometimes|numeric',
            'locationName' => 'sometimes|string|max:255',
            'price' => 'required|integer|min:0',
            'previousPrice' => 'sometimes|integer|min:0',
//            'discountType' => 'sometimes|in:usual,flash_sale,group',
            'currency' => 'sometimes|integer',
            'isHighlighted' => 'sometimes|boolean',
        ];
    }
}
