<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PermissionMiddleware
{
    public function handle(
        Request $request,
        Closure $next,
        string $permission
    ) {

        $user = $request->user();

        if (!$user || !$user->can($permission)) {

            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
