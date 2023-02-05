<?php

namespace App\Http\Controllers\Bank;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Bank\Payways as Payways;
use App\Model\Payment\PaymentSetting;
use App\Model\XinDream\Payways as XinPayways;

class Payway extends Controller
{

    private $limit = [
        ['type' => 1, 'limit' => [500, 1000, 3000, 5000, 6000]],
        ['type' => 2, 'limit' => [500, 1000, 3000, 5000, 10000, 20000, 30000]]
    ];

    /**
     * 取得金流管理列表
     *
     * @return object
     */

    public function getPaywayList()
    {

        $aPayways = Payways::getPaywaysList();
        foreach ($aPayways as $key => $value) {
            $aPayways[$key]['limit_option'] = (is_array($value['amount'])) ? max($value['amount']) : null;
            unset($aPayways[$key]['amount']);
        }

        return response()->json(['result' => true, 'data' => $aPayways]);
    }

    /**
     * 取得金流管理 - 下拉選單
     *
     * @return object
     */

    public function getPaywayOption()
    {

        $aOption = [];
        for ($i = 0; $i <= 1; $i++) {
            $aOption[$i]['type'] = $this->limit[$i]['type'];
            foreach ($this->limit[$i]['limit'] as $key => $value) {
                $aOption[$i]['limit'][$key]['id'] = $value;
                $aOption[$i]['limit'][$key]['name'] = $value;
            }
        }

        return response()->json(['result' => true, 'data' => $aOption]);
    }

    /**
     * 金流管理 - 修改
     *
     * @param Request $request
     * @return object
     */
    public function updatePayway(Request $request, XinPayways $_oXinPayways, Payways $_oPayways)
    {
        $aPayways = $_oPayways::getPaywaysListByType();
        $update = [];
        foreach ($request['setting_info'] as $key => $value) {
            $data['type'] = $value['type'];
            $data['active'] = $value['active'];
            $data['upper_limit'] = (in_array($value['type'], [1, 2])) ? $value['limit_option'] : $value['upper_limit'];
            $data['lower_limit'] = $value['lower_limit'];
            $data['amount'] = $aPayways[$value['type']]['amount'];
            $limitOpt = array_column($this->limit, 'limit', 'type');

            if ($aPayways[$value['type']]['active'] != $value['active']) {
                $update[$value['type']]['active'] = $value['active'];
            }

            switch ($value['type']) {
                // 超商
                case 1:
                    // ATM
                case 2:
                    // 處理 amount 依選定值 切取 amount array
                    $tmpLimit = $limitOpt[$value['type']];
                    $location = array_search($value['limit_option'], $tmpLimit);
                    $combAmount = array_slice($tmpLimit, 0, $location + 1);
                    // amount 異動時 取最大
                    if ($aPayways[$value['type']]['amount'] != $combAmount) {
                        $update[$value['type']]['amount'] = $combAmount;
                        $update[$value['type']]['upper_limit'] = max($combAmount);
                    }
                    break;
                // 銀行卡
                case 3:
                    // 最大值
                    if ($aPayways[$value['type']]['upper_limit'] != $value['upper_limit']) {
                        $update[$value['type']]['upper_limit'] = $value['upper_limit'];
                    }
                    // 最小值
                    if ($aPayways[$value['type']]['lower_limit'] != $value['lower_limit']) {
                        $update[$value['type']]['lower_limit'] = $value['lower_limit'];
                    }
                    break;
                default:
                    break;

            }
        }
        if (count($update) > 0) {
            foreach ($update as $id => $data) {
                $_oPayways::updatePayways($id, $data);

                $id = $id == 3 ? 4 : $id;
                $_oXinPayways::updateLimit($data, $id);
            }
        }

        return response()->json(['result' => true, 'data' => []]);
    }

    /**
     * 取得第三方支付 (1:綠界 2:藍新)
     *
     * @return object
     */

    public function getPaymentSet()
    {
        $payment_type = PaymentSetting::getPaymentSetting();
        return response()->json(['result' => true, 'data' => $payment_type]);
    }

}
