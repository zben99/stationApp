<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Station;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {

        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        if (session('needs_station_selection')) {

            return redirect()->route('station.selection');

        } else {

            $request->session()->regenerate();

            return redirect()->intended(route('dashboard', absolute: false));

        }

    }

    public function showSelectionPage()
    {
        $user = Auth::user();
        if (! $user->hasRole('Super Gestionnaire')) {
            return redirect()->route('dashboard');
        }

        $stations = Station::all();

        return view('auth.select-station', compact('stations'));
    }

    public function select(Request $request)
    {
        $user = Auth::user();
        if (! $user->hasRole('Super Gestionnaire')) {
            return redirect()->route('dashboard');
        }

        $request->validate([
            'station_id' => 'required|exists:stations,id',
        ]);

        session(['selected_station_id' => $request->station_id]);

        return redirect()->route('dashboard')->with('status', 'Station sélectionnée avec succès.');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
