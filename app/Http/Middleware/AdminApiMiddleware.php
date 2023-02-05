<?php

namespace App\Http\Middleware;

use App\Exceptions\CodeError\Middleware\AdminMiddlewareException;
use Closure;

class AdminApiMiddleware
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
            if (!$request->hasHeader('Authenticate-Key') || $request->header('Authenticate-Key') !== config('api.AuthenticateKey.Admin')) {
                throw new AdminMiddlewareException('AUTH_ERROR');
            }
        } catch (AdminMiddlewareException $e) {
            return response()->json(['result' => false, 'message' => $e->getMessage(), 'code' => $e->getCode()]);
        }

        return $next($request)->header('User', 'AdminApi');
    }
}
