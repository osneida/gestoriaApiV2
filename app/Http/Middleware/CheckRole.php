<?php

namespace App\Http\Middleware;

use Closure;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param array $roles
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        if ( ! in_array($request->user()->role_id, $roles)) {
            return response()->json(["message" => "access denied"], 401);
        }
        return $next($request);
    }
}
