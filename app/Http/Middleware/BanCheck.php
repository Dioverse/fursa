<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BanCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!in_array($request->user()->role, $roles)) {
        }
        if ($request->user()->ban == 1) {
            return response()->json([
                'redirect'   => 'banned',
                'message' => 'banned'
                // 'message' => 'Your account has been banned. Contact support'
            ], 401);
        }

        return $next($request);
    }
}
