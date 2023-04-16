<?php

namespace App\Http\Requests\Ticket;

use Illuminate\Foundation\Http\FormRequest;

class StoreMatchAlertRules extends FormRequest
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
            'fromAirport' => 'integer|nullable',
            'toAirport' => 'integer|nullable',
            'adultsCount' => 'integer|nullable',
            'childrenCount' => 'integer|nullable',
            'infantsCount' => 'integer|nullable',
            'isOneWay' => 'nullable',
            'classType' => 'nullable'
        ];
    }
}
