<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param $role
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        $user = auth()->user();

        if (! $user->hasRole($role)) {
            return response()->json([
               'error' => [
                   'message' => 'You do not have clearance to perform this action.',
                   'status' => 'Fail'
               ]
            ]);
        }
        return $next($request);
    }
}
