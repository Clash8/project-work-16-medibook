<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Consente l'accesso alla rotta solo agli utenti che ricoprono uno dei ruoli indicati. */
class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$ruoli): Response
    {
        abort_unless(
            $request->user() && in_array($request->user()->ruolo, $ruoli, true),
            403,
            'Il ruolo dell\'utente non consente questa operazione.'
        );

        return $next($request);
    }
}
