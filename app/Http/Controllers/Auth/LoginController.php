<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/admin';

    /** Nombre d'essais avant blocage temporaire. */
    protected $maxAttempts = 5;

    protected $decayMinutes = 5;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /** Seuls les comptes actifs peuvent se connecter. */
    protected function credentials(Request $request): array
    {
        return $request->only($this->username(), 'password') + ['active' => true];
    }

    protected function authenticated(Request $request, $user)
    {
        $user->forceFill(['last_login_at' => now()])->save();
    }

    protected function loggedOut(Request $request)
    {
        return redirect()->route('home');
    }
}
