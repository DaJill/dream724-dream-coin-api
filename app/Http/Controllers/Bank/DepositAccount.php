<?php

namespace App\Http\Controllers\Bank;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Exceptions\CodeError\Bank\BankException;
use App\Model\Bank\DepositAccounts;

class DepositAccount extends Controller
{
    /**
     * 取得存款帳戶管理 - 列表
     *
     * @return object
     */

    public function getDepositAccount()
    {

        $aDepositAccount = DepositAccounts::getDepositAccounts();

        foreach ($aDepositAccount as $key => $value) {
            $value['operation_bank_info'] = [
                'code'    => $value['code'],
                'branch'  => $value['branch'],
                'account' => $value['account'],
                'name'    => $value['name']
            ];
        }

        return response()->json(['result' => true, 'data' => $aDepositAccount]);
    }

    /**
     * 新增存款帳戶
     *
     * @param Request $request
     *
     * @return object
     */

    public function createDepositAccount(Request $request)
    {
        //檢查銀行帳戶是否存在
        $bCheckAccount = DepositAccounts::checkDepositAccount($request['bank_id'],$request['account']);
        if($bCheckAccount){
            throw new BankException('DEPOSIT_ACCOUNT_IS_EXIST');
        }

        $result = DepositAccounts::createDepositAccount($request->all());
        
        return response()->json(['result' => true, 'data' => $result]);
    }

    /**
     * 修改存款帳戶
     *
     * @param integer $id
     * @param Request $request
     *
     * @return object
     */

    public function updateDepositAccount($id,Request $request)
    {
        //檢查銀行帳戶是否存在
        $bCheckAccount = DepositAccounts::checkDepositAccount($request['bank_id'],$request['account'],$id);
        if($bCheckAccount){
            throw new BankException('DEPOSIT_ACCOUNT_IS_EXIST');
        }

        $result = DepositAccounts::updateDepositAccount($id,$request->all());

        return response()->json(['result' => true, 'data' => $result]);
    }

    /**
     * 刪除存款帳戶
     *
     * @param integer $id
     *
     * @return object
     */

    public function deleteDepositAccount($id)
    {

        // todo: 驗證帳戶是否有尚未處理訂單 (等會員部分實作完再補上)

        // 驗證帳戶狀態
        $check = DepositAccounts::getDepositAccountsById($id);
        // 帳戶開啟時 禁止刪除
        if ($check['active'] == 1) {
            throw new BankException('DEPOSIT_ACCOUNT_IS_STATE_NOT_ALLOW');
        }

        // 『審核中存點』項目中的數值不為0也禁止刪除
        if ($check['debit'] > 0) {
            throw new BankException('DEPOSIT_ACCOUNT_IS_DEBIT_NOT_ZERO');
        }

        $result = DepositAccounts::deleteDepositAccount($id);

        return response()->json(['result' => true, 'data' => $result]);
    }

    /**
     * 更換存款帳戶 - 並關閉其他帳戶
     *
     * @param integer $id
     *
     * @return object
     */

    public function changeAccountActive($id)
    {

        // todo: 驗證帳戶是否有尚未處理訂單 (等會員部分實作完再補上)

        // 停用開啟的帳戶
        DepositAccounts::disableDepositAccount();
        // 開啟指定的帳戶
        $result = DepositAccounts::updateDepositAccount($id,['active' => 1]);

        return response()->json(['result' => true, 'data' => $result]);
    }

    /**
     * 清空存款帳戶
     *
     * @param integer $id
     *
     * @return object
     */

    public function clearAccountCredit($id)
    {

        // todo: 驗證帳戶是否有尚未處理訂單 (等會員部分實作完再補上)

        // 清空存款帳戶
        $result = DepositAccounts::updateDepositAccount($id,['credit' => 0]);

        return response()->json(['result' => true, 'data' => $result]);
    }

}
