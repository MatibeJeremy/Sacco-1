<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use JWTAuth;
use Illuminate\Http\Request;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        }catch (Exception $exception){
            if ($exception instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return response()->json([
                    'error' => [
                        'message' => 'Token is Invalid.',
                        'status' => 'Fail'
                    ]
                ], 401);
            }else if ($exception instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return response()->json([
                    'error' => [
                        'message' => 'Token is Expired.',
                        'status' => 'Fail'
                    ]
                ], 401);
            }else{
                return response()->json([
                    'error' => [
                        'message' => $exception->getMessage(),
                        'status' => 'Fail'
                    ]
                ], 401);
            }
        }
        return $next($request);
    }
}
