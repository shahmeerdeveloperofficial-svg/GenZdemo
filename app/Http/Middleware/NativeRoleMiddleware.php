<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class NativeRoleMiddleware
{
    public function handle(Request $request, Closure $next, int $role)
    {
        $user = $request->user();

        abort_unless($user && (int) $user->role === $role, 403);

        if ((int) $user->status !== USER_STATUS_ACTIVE) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('error', __('Your account is inactive. Please contact support.'));
        }

        return $next($request);
    }
}
