<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Accès au back-office : super admin ou administrateur de club actif. */
class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->guest(route('login'));
        }

        if (! $user->canAccessAdmin()) {
            abort(403, "Votre compte n'a pas accès à l'administration.");
        }

        return $next($request);
    }
}
