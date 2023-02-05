<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * 統整要回傳前端的資料格式.
     *
     * @param array $info
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function responseWithJson(array $info)
    {
        // 整理傳入參數
        $apiCode = ($info['code'] && strlen($info['code']) == 6) ? $info['code'] : config('apiCode.notAPICode'); // 回傳前端的 api code
        $result = $info['result'] ?? ''; // 回傳前端的資料
        $error = $info['error'] ?? ''; // 錯誤訊息(紀錄 log 使用)

        // 整理要回傳的格式
        $statusCode = substr($apiCode, 0, 3);
        $response = ['result' => $result, 'code' => $apiCode];
        if ($statusCode != 200) { // 非 200 的一律寫入到 log
            $this->addErrorLog($response, $error);
            if (env('APP_DEBUG') == true) {
                $response['error'] = $error;
            }
        }

        return response()->json($response, $statusCode);
    }
}
