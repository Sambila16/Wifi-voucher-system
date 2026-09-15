<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->is_active || ! in_array($user->role, ['admin', 'super_admin', 'cashier'])) {
            abort(403, 'You do not have access to the admin panel.');
        }

        return $next($request);
    }
}
