<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CanAnyPermission
{
    public function handle(Request $request, Closure $next, string ...$abilities)
    {
        abort_unless(Gate::forUser($request->user())->any($abilities), 403);

        return $next($request);
    }
}
