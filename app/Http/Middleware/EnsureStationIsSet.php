<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureStationIsSet
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user) {
            // ✅ Ne rien faire si c'est un admin
            if ($user->hasRole('Admin')) {
                return $next($request);
            }

            // ✅ Si ce n'est pas un admin, injecter station_id s'il n'est pas déjà en session
            if (! session()->has('selected_station_id') && $user->station_id) {
                session(['selected_station_id' => $user->station_id]);
            }
        }

        return $next($request);
    }
}
