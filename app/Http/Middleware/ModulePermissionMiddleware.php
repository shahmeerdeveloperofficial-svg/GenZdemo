<?php

namespace App\Http\Middleware;

use App\Services\RolePermissionService;
use Closure;
use Illuminate\Http\Request;

class ModulePermissionMiddleware
{
    public function __construct(private readonly RolePermissionService $permissions)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        $module = $this->permissions->moduleForRoute($request->route()?->getName());
        if (!$module) {
            return $next($request);
        }

        $action = $this->permissions->actionForRequest($request);
        abort_unless($this->permissions->allows($request->user(), $module . '.' . $action), 403);

        return $next($request);
    }
}
