<?php

namespace App\Http\Middleware;

use App\Exceptions\CodeError\Middleware\MemXinMiddlewareException;
use Closure;

class MemXinApiMiddleware
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
            if (!$request->hasHeader('Authenticate-Key') || $request->header('Authenticate-Key') !== config('api.AuthenticateKey.MemXin')) {
                throw new MemXinMiddlewareException('AUTH_ERROR');
            }

            //取得白名單
            if(config('app.env') == 'production') {
                if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
                    $sIPTmp = $_SERVER["HTTP_CLIENT_IP"];
                } elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
                    $sIPTmp = $_SERVER["HTTP_X_FORWARDED_FOR"];
                } else {
                    $sIPTmp = $_SERVER["REMOTE_ADDR"];
                }
                $sIPTmp = explode(',', $sIPTmp);
                $sIp = trim($sIPTmp[0]);
                if(!in_array($sIp, config('white_ip.mem_xin'))) {
                    throw new MemXinMiddlewareException('IP_NOT_IN_WHITE_LIST', [$sIp]);
                }
            }
        } catch (MemXinMiddlewareException $e) {
            return response()->json(['result' => false, 'message' => $e->getMessage(), 'code' => $e->getCode()]);
        }

        return $next($request)->header('User', 'MemXinApi');
    }
}
