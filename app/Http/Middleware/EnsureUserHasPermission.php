<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasPermission
{
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = $request->user();

        abort_unless($user && $user->hasAnyPermission($permissions), 403, 'Vous n’avez pas l’autorisation d’effectuer cette action.');

        return $next($request);
    }
}
