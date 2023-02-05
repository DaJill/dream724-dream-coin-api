<?php

namespace App\Http\Middleware;

use Closure;
use Fluent;

class SetHeaderMiddleware
{
    /**
     * 處理傳入的請求
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // RoutePath
        $uri = $request->route()->uri;
        if(preg_match_all("/\{(\w+)\}/", $uri, $matches)) {
            foreach($matches[1] as $qKey) {
                if(isset($request->$qKey) && !empty($request->$qKey)) {
                    $uri = str_replace("{".$qKey."}", $request->$qKey, $uri);
                }
            }
        }

        
        // Args
        $query = $request->all();
        $query_string = "";        
        foreach ($query as $k=>$v) {
            if (is_array($v)) {
                // avoid nginx log replace " to \x22
                $v = str_replace("\"", "'", json_encode($v));
            }
            if (preg_match("/password/i", $k)) {
                $v = '******';
            }
            $query_string .= empty($query_string)? "$k=$v" : "&$k=$v";
        }
        
        // set to response header
        $response = $next($request);
        
        $response->headers->set('RoutePath', $uri);
        $response->headers->set('Args', substr($query_string, 0, 2048));
        return $response;
    }
}
