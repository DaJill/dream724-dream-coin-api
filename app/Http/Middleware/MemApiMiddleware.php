<?php

namespace App\Http\Middleware;

use App\Exceptions\CodeError\Middleware\MemMiddlewareException;
use Closure;

class MemApiMiddleware
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

        try {
            if (!$request->hasHeader('Authenticate-Key') || $request->header('Authenticate-Key') !== config('api.AuthenticateKey.Mem')) {
                throw new MemMiddlewareException('AUTH_ERROR');
            }
        } catch (MemMiddlewareException $e) {
            return response()->json(['result' => false, 'message' => $e->getMessage(), 'code' => $e->getCode()]);
        }

        return $next($request)->header('User', 'MemApi');
    }
}
