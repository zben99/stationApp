<?php

namespace App\Http\Controllers\Auth;

use App\Models\Station;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Auth\LoginRequest;

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

        }else {

            $request->session()->regenerate();

            return redirect()->intended(route('dashboard', absolute: false));

        }


    }

    public function showSelectionPage()
    {
        $user = Auth::user();
        if (!$user->hasRole('Admin')) {
            return redirect()->route('dashboard');
        }

        $stations = Station::all();
        return view('auth.select-station', compact('stations'));
    }

    public function select(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasRole('Admin')) {
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
