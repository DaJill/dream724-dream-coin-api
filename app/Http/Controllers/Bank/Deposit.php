<?php

namespace App\Http\Controllers\Bank;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Exceptions\CodeError\Bank\DepositException;
use App\Model\Bank\UsersDeposit;
use App\Model\Bank\DepositAccounts;
use DB;
use Auth;

class Deposit extends Controller
{
    private $option = [1 => '審核中', 2 => '已成功', 3 => '已取消'];
    private $payOption = [0 => '全部', 1 => '超商付款', 2 => 'ATM', 3 => '銀行卡'];
    /*
    前端按鈕 => DB欄位(status)
    1:確定 => 1
    2:取消 => 3
    */

    private $action = [1 => 1, 2 => 3];


    /**
     * 取得存款管理&入點管理 - 訂單狀態 - 下拉選單
     *
     * @param Request $request
     *
     * @return object
     */

    public function getOption(Request $request)
    {
        $aOption = [];
        $option = $this->option;

        //判斷參數是否帶入錯誤
        if (!in_array($request['condition'], ['deposit', 'income'])) {
            throw new DepositException('DEPOSIT_DROPDOWN_IS_ERROR');
        }

        if ($request['condition'] == 'deposit') {
            unset($option[2]);
        }
        $i = 0;
        foreach ($option as $key => $value) {
            $aOption[$i]['id'] = $key;
            $aOption[$i]['name'] = $value;
            $i++;
        }

        return response()->json(['result' => true, 'data' => $aOption]);
    }

    /**
     * 取得存款管理&入點管理 - 訂單付款類型 - 下拉選單
     *
     * @param Request $request
     *
     * @return object
     */

    public function getPayOption(Request $request)
    {
        $aOption = [];
        $option = $this->payOption;

        //判斷參數是否帶入錯誤
        if (!in_array($request['condition'], ['deposit', 'income'])) {
            throw new DepositException('DEPOSIT_DROPDOWN_IS_ERROR');
        }

        if ($request['condition'] == 'deposit') {
            unset($option[1]);
            unset($option[2]);
        }
        $i = 0;
        foreach ($option as $key => $value) {
            $aOption[$i]['id'] = $key;
            $aOption[$i]['name'] = $value;
            $i++;
        }

        return response()->json(['result' => true, 'data' => $aOption]);
    }

    /**
     * 取得存款管理 - 總計
     *
     * @param integer $status  [狀態 1：審核，3：取消]
     * @param Request $request
     *
     * @return object
     */

    public function getDepositCount($status,Request $request)
    {

        $table_name = ['users_deposit.','users.'];
        $aQuery = [];
        $aTimeQuery = [];
        $aQuery[$table_name[0].'type'] = 1;
        $aQuery[$table_name[0].'status'] = $status;

        if ($request->has('no')) {
            $aQuery[$table_name[0].'no'] = $request['no'];
        }

        if ($request->has('account')) {
            $aQuery[$table_name[1].'account'] = $request->input('account');
            if (is_numeric($request->input('account'))) {
                $aQuery[$table_name[1].'mobile'] = $request->input('account');
                unset($aQuery[$table_name[1].'account']);
            }
        }

        // 搜全部的付款狀態
        if ($request->has('payway_id')) {
            if($request['payway_id'] != 0) {
                $aQuery[$table_name[0] . 'payway_id'] = $request['payway_id'];
            }
        }

        // 存款訂單 - 已取消 必帶日期
        if ($status == 3) {

            if ($request['start_time'] == null || $request['end_time'] == null) {
                throw new DepositException('DEPOSIT_DATE_IS_NOT_NULL');
            }

            $aTimeQuery['start'] = $request['start_time'];
            $aTimeQuery['end'] = $request['end_time'];
        }

        $result = UsersDeposit::getDepositCount($aQuery,$aTimeQuery);
        $result[0]['total'] = ($result[0]['total'] == null) ? '0' : $result[0]['total'];

        return response()->json(['result' => true, 'data' => $result]);
    }

    /**
     * 取得存款管理 - 列表
     *
     * @param integer $status  [狀態 1：審核，3：取消]
     * @param Request $request
     *
     * @return object
     */

    public function getDepositList($status,Request $request)
    {
        $table_name = ['users_deposit.','users.'];
        $aQuery = [];
        $aTimeQuery = [];
        $aQuery[$table_name[0].'type'] = 1;
        $aQuery[$table_name[0].'status'] = $status;
        $digit = [-5,5];

        if ($request->has('no')) {
            $aQuery[$table_name[0].'no'] = $request['no'];
        }

        if ($request->has('account')) {
            $aQuery[$table_name[1].'account'] = $request->input('account');
            if (is_numeric($request->input('account'))) {
                $aQuery[$table_name[1].'mobile'] = $request->input('account');
                unset($aQuery[$table_name[1].'account']);
            }
        }

        // 搜全部的付款狀態
        if ($request->has('payway_id')) {
            if($request['payway_id'] != 0) {
                $aQuery[$table_name[0] . 'payway_id'] = $request['payway_id'];
            }
        }

        // 存款訂單 - 已取消 必帶日期
        if ($status == 3) {

            if ($request['start_time'] == null || $request['end_time'] == null) {
                throw new DepositException('DEPOSIT_DATE_IS_NOT_NULL');
            }

            $aTimeQuery['start'] = $request['start_time'];
            $aTimeQuery['end'] = $request['end_time'];
        }

        $result = UsersDeposit::getDepositList($aQuery,$aTimeQuery);

        foreach ($result as $key => $value){
            $result[$key]['review_admin'] = ($value['admin'] == null) ? '' : $value['admin']['account'];
            $result[$key]['operation_bank_info'] = array_merge($value['payway_info'], ['deposit_to' => $value['deposit_info']['code'].'-'.$value['deposit_info']['bank'].'-'.mb_substr($value['deposit_info']['account'], $digit[0], $digit[1], 'UTF-8') ]);
            unset($value['admin']);
            unset($value['payway_info']);
            unset($value['third_party_info']);
            unset($value['deposit_info']);
        }

        return response()->json(['result' => true, 'data' => $result]);
    }

    /**
     * 訂單審核 [操作狀態 1：確定，2：取消]
     *
     * @param integer $id
     * @param Request $request
     *
     * @return object
     */

    public function updateDeposit($id, Request $request)
    {
        try {
            $status = $this->action[$request['action']];

            if($status == 3 && $request['reason'] == null){
                throw new DepositException('DEPOSIT_REASON_IS_NOT_NULL');
            }

            $aQuery = UsersDeposit::getDepositById($id);

            if ($aQuery['status'] != 1 && $aQuery['payway_status'] != 1) {
                throw new DepositException('DEPOSIT_STATE_IS_ERROR');
            }

            $update = [
                'status'         => $status,
                'review_user_id' => Auth::user()->id,
                'review_at'      => date('Y-m-d H:i:s'),
                'payway_status'  => ($status == 1) ? 2 : 3,
                'reason'         => ($status == 3) ? $request['reason'] : '',
            ];

            // 更新訂單
            $result = DB::transaction(function () use ($id, $aQuery, $update) {
                UsersDeposit::updateDeposit($id, $update);
                // 已取消狀態時，減掉存款帳戶管理的審核中存點
                if ($update['status'] == 3 && $aQuery['payway_id'] == 3) {
                    // 更新存款帳戶管理::審核中存點
                    DepositAccounts::updateDebit($aQuery['deposit_account_id'], $aQuery['credit']);
                }
                return true;
            });

            return response()->json(['result' => true, 'data' => $result]);
        } catch (\Exception $e) {
            throw new DepositException('DEPOSIT_UPDATE_IS_ERROR');
        }
    }

}
