<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        abort_unless((int) $request->user()?->role === USER_ROLE_ADMIN, 403);

        return $next($request);
    }
}
