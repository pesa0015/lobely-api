<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;

class ToUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $authorization = $request->header('Authorization');

        $token = str_replace('Bearer ', '', $authorization);

        $user = JWTAuth::toUser($token);
        $request->attributes->user = $user;

        return $next($request);
    }
}
