<?php

namespace App\Http\Middleware;

use Closure;

class SetJwt
{
    /**
     * 設置 JWT 設置
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $type = '')
    {
        switch ($type) {
            case 'admin': // 後台
                config(['auth.providers.users.model' => 'App\Model\Admin\AdminUser']);
                config(['jwt.user' => 'App\Model\Admin\AdminUser']);
                config(['jwt.secret' => config('jwt.secret_auth.admin')]);
                break;
            case 'mem': // 網站
                config(['auth.providers.users.model' => 'App\Model\User\Users']);
                config(['jwt.user' => 'App\Model\User\Users']);
                config(['jwt.secret' => config('jwt.secret_auth.mem')]);
                break;
            case 'mem_xin': // xin網站
                break;
            default:
        }
        return $next($request);
    }
}
