<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        abort_unless($user && $user->hasAnyRole($roles), 403, 'Vous n’avez pas le rôle requis pour accéder à cette page.');

        return $next($request);
    }
}
