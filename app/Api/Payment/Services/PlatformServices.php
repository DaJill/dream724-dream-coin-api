<?php

namespace App\Api\Payment\Services;

use GuzzleHttp\Client;

class PlatformServices
{
    // 平台給予的encrypt Key
    private $encryptKey = 'B47YTH3FZlGbGmfI';
    // 平台給予的API Key
    private $apiKey = 'cf5fe5d5-8158-4db2-a87b-6eb03b0072aa';

    /**
     * 入點到平台
     *
     * @param string $account
     * @param integer $credit
     * @param string $callback_code
     *
     * @return object
     */
    public function transfer($account,$credit,$callback_code)
    {
        // API 網址
        $url = config('api.url.platform_api').'/api_jfa/v1/transfer/credit';

        $transferNo = 'XIN' . rand(10000000, 99999999);
        // API 需要傳遞的參數
        $param = [
            'account' => $account,
            'no' => $transferNo,
            'type' => 'IN',
            'credit' => $credit,
            'time' => date('YmdHis'),
            'system' => 1,
            'xin_no' => $callback_code
        ];

        // 取得加密後的資訊
        $encrypt = $this->doEncrypt(http_build_query($param), $this->encryptKey);

        // Call API (使用 GuzzleHttp 套件)
        $client = new Client();
        $response = $client->post($url, [
            'headers' => [
                'x-api-key' => $this->apiKey,
            ],
            'form_params' => [
                'param' => $encrypt,
            ]
        ]);
        return json_decode($response->getBody(),true);
    }

    /**
     * 傳遞第三方支付繳費資訊
     *
     * @param string $callback_code
     * @param array $info
     *
     * @return object
     */
    public function transferInfo($callback_code,$info)
    {
        // API 網址
        $url = config('api.url.platform_api').'/api_jfa/v1/transfer/info';

        // API 需要傳遞的參數
        $param = [
            'xin_no' => $callback_code,
            'info' => $info,
            'time' => date('YmdHis'),
        ];

        // 取得加密後的資訊
        $encrypt = $this->doEncrypt(http_build_query($param), $this->encryptKey);

        // Call API (使用 GuzzleHttp 套件)
        $client = new Client();
        $response = $client->post($url, [
            'headers' => [
                'x-api-key' => $this->apiKey,
            ],
            'form_params' => [
                'param' => $encrypt,
            ]
        ]);
        return json_decode($response->getBody(),true);
    }

    /**
     * 取消平台逾期儲值單
     *
     * @param array $xin_no
     *
     * @return object
     */
    public function cancel($xin_no)
    {
        // API 網址
        $url = config('api.url.platform_api').'/api_jfa/v1/transfer/cancel';

        // API 需要傳遞的參數
        $param = [
            'xin_no' => $xin_no,
            'time' => date('YmdHis'),
        ];

        // 取得加密後的資訊
        $encrypt = $this->doEncrypt(http_build_query($param), $this->encryptKey);

        // Call API (使用 GuzzleHttp 套件)
        $client = new Client();
        $response = $client->post($url, [
            'headers' => [
                'x-api-key' => $this->apiKey,
            ],
            'form_params' => [
                'param' => $encrypt,
            ]
        ]);
        return json_decode($response->getBody(),true);
    }

    /**
     * 參數加密
     *
     * @param string $str
     * @param string $encryptKey
     * @return string
     */
    private function doEncrypt($str, $encryptKey)
    {
        return base64_encode(openssl_encrypt($str, 'AES-256-CBC', $encryptKey, OPENSSL_RAW_DATA, $encryptKey));
    }

}
