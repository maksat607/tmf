<?php

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ticket\StoreMatchAlertRules;
use App\Http\Resources\Ticket\TicketsMatchAlertRuleResource;
use App\Models\TicketsMatchAlertRule;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class MatchAlertRulesController extends Controller
{
    /**
     * Get a list of all match alert rules.
     *
     * @param  Request  $request
     * @return TicketsMatchAlertRuleResource[]
     */
    public function index(Request $request): array
    {
        return TicketsMatchAlertRuleResource::collection(TicketsMatchAlertRule::with(['fromAirport', 'toAirport'])->get())->toArray($request);
    }

    /**
     * Create a new match alert rule.
     *
     * @param  StoreMatchAlertRules  $request
     * @return TicketsMatchAlertRuleResource
     */
    public function store(StoreMatchAlertRules $request): TicketsMatchAlertRuleResource
    {
        $ticketsMatchAlertRule = TicketsMatchAlertRule::create(array_merge($request->validated(), ['user_id' => auth()->user()?->id]));
        return new TicketsMatchAlertRuleResource($ticketsMatchAlertRule->loadMissing(['fromAirport', 'toAirport']));
    }

    /**
     * Update an existing match alert rule.
     *
     * @param  StoreMatchAlertRules  $request
     * @param  TicketsMatchAlertRule  $matchAlertRule
     * @return TicketsMatchAlertRuleResource
     * @throws AuthorizationException
     */
    public function update(StoreMatchAlertRules $request, TicketsMatchAlertRule $matchAlertRule): TicketsMatchAlertRuleResource
    {
        $this->authorize('update', $matchAlertRule);

        $matchAlertRule->update($request->validated());

        return new TicketsMatchAlertRuleResource($matchAlertRule->loadMissing(['fromAirport', 'toAirport']));
    }

    /**
     * Delete an existing match alert rule.
     *
     * @param  Request  $request
     * @param  TicketsMatchAlertRule  $matchAlertRule
     * @return void
     * @throws AuthorizationException
     */
    public function destroy(Request $request, TicketsMatchAlertRule $matchAlertRule): void
    {
        $this->authorize('delete', $matchAlertRule);

        $matchAlertRule->delete();
    }
}
