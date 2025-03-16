<?php

namespace RomegaSoftware\WorkOSTeams\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * @api
 */
class EnsureHasTeam
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip middleware if user is not authenticated
        if (! Auth::check()) {
            return $next($request);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Ensure the user is loaded with the currentTeam relationship
        if (! $user->relationLoaded('currentTeam')) {
            $user->load('currentTeam');
        }

        // Skip if we're already on the teams.create route
        if ($request->routeIs('teams.create') || $request->routeIs('livewire.update')) {
            return $next($request);
        }

        // If the user doesn't have a current team, but has teams, set the first one as current
        if ($user->getAttribute('current_team_id') === null) {
            $teams = $user->allTeams();

            if ($teams->isNotEmpty()) {
                $user->switchTeam($teams->first());
            } else {
                // If the user doesn't have any teams, redirect to create one
                return redirect()->route('teams.create')
                    ->with('warning', 'Please create a team to continue.');
            }
        }

        return $next($request);
    }
}
