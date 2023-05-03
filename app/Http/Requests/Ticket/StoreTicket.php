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
            'startDateAt' => 'sometimes|regex:/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.\d{3}Z$/',
            'endDateAt' => 'sometimes|regex:/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.\d{3}Z$/',
            'returnStartDateAt' => 'sometimes|regex:/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.\d{3}Z$/',
            'returnEndDateAt' => 'sometimes|regex:/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.\d{3}Z$/',
            'stopsCount' => 'required|integer',
            'returnStopsCount' => 'sometimes|integer',
            'classType' => 'required|in:economy,business,first',
            'adultsCount' => 'required|integer|min:1',
            'childrenCount' => 'required|integer|min:0',
            'infantsCount' => 'required|integer|min:0',
            'airline' => 'required|integer',
            'locationLatitude' => 'required|numeric',
            'locationLongitude' => 'required|numeric',
            'locationName' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'previousPrice' => 'required|integer|min:0',
            'discountType' => 'required|in:usual,flash_sale,group',
            'currency' => 'required|integer',
            'isHighlighted' => 'required|boolean',
        ];
    }
}