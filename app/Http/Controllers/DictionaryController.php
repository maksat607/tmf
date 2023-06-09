<?php

namespace App\Http\Controllers;

use App\Http\Resources\Airline\AirlineResource;
use App\Http\Resources\Airport\AirportResource;
use App\Http\Resources\Currency\CurrencyResource;
use App\Models\DictionaryAirline;
use App\Models\DictionaryAirport;
use App\Models\DictionaryCurrency;
use Illuminate\Http\Request;

class DictionaryController extends Controller
{
    public function airports(Request $request)
    {
        return AirportResource::collection(
            DictionaryAirport::when($request->get('offset'), function ($query) use ($request) {
                $query->skip($request->get('offset'));
            })
                ->when($request->get('limit'), function ($query) use ($request) {
                    $query->take($request->get('limit'));
                })
                ->when($request->get('query'), function ($query) use ($request) {
                    $query->where('code', 'LIKE', "%" . $request->get('query') . "%")
                        ->orWhere('name', 'LIKE', "%" . $request->get('query') . "%")
                        ->orWhere('country_code', 'LIKE', "%" . $request->get('query') . "%")
                        ->orWhere('country_name', 'LIKE', "%" . $request->get('query') . "%")
                        ->orWhere('city_code', 'LIKE', "%" . $request->get('query') . "%")
                        ->orWhere('translations', 'LIKE', "%" . $request->get('query') . "%");
                })
                ->get()
        );
    }

    public function airlines(Request $request)
    {
        return AirlineResource::collection(
            DictionaryAirline::when($request->get('offset'), function ($query) use ($request) {
                $query->skip($request->get('offset'));
            })
                ->when($request->get('limit'), function ($query) use ($request) {
                    $query->take($request->get('limit'));
                })
                ->when($request->get('query'), function ($query) use ($request) {
                    $query->where('name', 'LIKE', "%" . $request->get('query') . "%")
                        ->orWhere('site', 'LIKE', "%" . $request->get('query') . "%")
                        ->orWhere('iata_code', 'LIKE', "%" . $request->get('query') . "%")
                        ->orWhere('icao_code', 'LIKE', "%" . $request->get('query') . "%");
                })
                ->get()
        );
    }

    public function currencies(Request $request)
    {
        return CurrencyResource::collection(
            DictionaryCurrency::when($request->get('offset'), function ($query) use ($request) {
                $query->skip($request->get('offset'));
            })
                ->when($request->get('limit'), function ($query) use ($request) {
                    $query->take($request->get('limit'));
                })
                ->when($request->get('query'), function ($query) use ($request) {
                    $query->where('code', 'LIKE', "%" . $request->get('query') . "%")
                        ->orWhere('symbol', 'LIKE', "%" . $request->get('query') . "%");
                })
                ->get()
        );
    }
}
