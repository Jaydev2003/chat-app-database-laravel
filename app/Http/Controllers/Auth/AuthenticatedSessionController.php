<?php

namespace App\Http\Controllers\Auth;

use App\Events\UserStatusUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
   
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
    $user = Auth::user();


    $user->update(['last_seen' => now()]);

    Cache::put('user-online-' . $user->id, true, now()->addMinutes(5));


    broadcast(new UserStatusUpdated($user));

    $request->session()->regenerate();

    return redirect()->intended(route('dashboard', absolute: false));
}


    
    public function destroy(Request $request): RedirectResponse
{
    $user = Auth::user();

    if ($user) {
        $user->update(['last_seen' => now()]);

        Cache::forget('user-online-' . $user->id);

        broadcast(new UserStatusUpdated($user));
    }

    Auth::guard('web')->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/');
}
 

    
    
}
