<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\User\Users;
use App\Model\User\UsersActiveToken;
use App\Model\User\UserUsualAddress;
use App\Model\Product\ProductOrderList;
use App\Exceptions\CodeError\User\UserException;
use Hash;
use Mail;
use Carbon\Carbon;

class User extends Controller
{
    /**
     * 購物網建立使用者(平台ID寫死)
     */
    public function createMemAccount(
        Request $_oRequest, 
        Users $_oUsers, 
        UsersActiveToken $_oUsersActiveToken
    )
    {
        $sAccount = $_oRequest->input('account');//帳號
        $sPassword = $_oRequest->input('password');//密碼
        $sMobile = $_oRequest->input('mobile', null);//手機
        $sEmail = $_oRequest->input('email');//密碼
        $sNickname = $_oRequest->input('nickname', '');//暱稱
        $iHallId = 1; //購物網ID
        $sActive = 1; //預設啟用
        $sTokenActive = 0; //mail認證預設未認證
      
        //檢查mail是否重複，重複不給新增
        $aUser = $_oUsers::getList(['id'], ['email'=>$sEmail, 'hallid'=>$iHallId]);
        if(!empty($aUser)) {
            throw new UserException('ACCOUNT_MAIL_EXIST');
        }
        //新增使用者, 以及mail驗證
        if(!$iUserId = $_oUsers::add( $iHallId, $sAccount, Hash::make($sPassword), $sMobile, $sEmail, $sNickname, $sActive, $sTokenActive)) {
            throw new UserException('ACCOUNT_EXIST');
        }
        
        $iDeadlineDays = 1;//1天
        $sToken = Hash::make($sAccount.$sPassword.$sEmail);
        $sToken = str_replace('/', '', $sToken); //去掉斜線，不然客端連結會有問題
        $_oUsersActiveToken::add($iUserId, $sToken, $iDeadlineDays);
        
        $sDomain = 'http://dream-wr.net/member/confirm?token='.$sToken;
        $aViewData = [
            'domain' => $sDomain, 
            'account' => $sAccount,
        ];

        //寄信
        $this->sendMail (
            $sEmail, 
            '感謝註冊圓夢, 請確認信箱並啟用帳號', 
            'emails.user_confirm', 
            $aViewData
        );
        return ['result' => true, 'data' => []];
    }

    public function resendCreateMail(
        Request $_oRequest, 
        Users $_oUsers, 
        UsersActiveToken $_oUsersActiveToken
    )
    {
        $aFieldsCondition['email'] = $_oRequest->input('email');//帳號
        $aFieldsCondition['active'] = 1; //啟用
        $aFieldsCondition['hallid'] = 1; //圓夢
        $aFieldsCondition['token_active'] = 0; //未激活
        $aUser = $_oUsers::getList(['id', 'email', 'account'], $aFieldsCondition);
        if(empty($aUser)) {//比對不到email、啟用且未激活的使用者
            throw new UserException('ACCOUNT_RESEND_USER_NOT_EXIST');
        }
        $aUser = $aUser[0];
        $iDeadlineDays = 1;//1天
        $sToken = Hash::make($aUser['account'].$aUser['email']);
        $sToken = str_replace('/', '', $sToken); //去掉斜線，不然客端連結會有問題
        $_oUsersActiveToken::add($aUser['id'], $sToken, $iDeadlineDays);
        $sDomain = 'http://dream-wr.net/member/confirm?token='.$sToken;
        $aViewData = [
            'domain' => $sDomain, 
            'account' => $aUser['account'],
        ];

        //寄信
        $this->sendMail (
            $aUser['email'],
            '感謝註冊圓夢, 請確認信箱並啟用帳號', 
            'emails.user_confirm', 
            $aViewData
        );
        return ['result' => true, 'data' => []];
    }

    /**
     * 激活帳號
     *
     * @param Request $_oRequest
     * @param Users $_oUsers
     * @param UsersActiveToken $_oUsersActiveToken
     * @param [type] $_sToken
     * @return void
     */
    public function activeAccount(
        $_sToken, 
        Users $_oUsers, 
        UsersActiveToken $_oUsersActiveToken
    )
    {
        $aToken = $_oUsersActiveToken::getByToken($_sToken);
        $iNow = strtotime(Carbon::now()->toDateTimeString());

        if(empty($aToken)) { //驗證碼不存在
            throw new UserException('ACCOUNT_ACTIVE_TOKEN_EMPTY');
        }

        if($iNow > strtotime($aToken['deadline'])) { //超過驗證時間
            throw new UserException('ACCOUNT_ACTIVE_DEADLINE_ERROR');
        }

        if($aToken['token_active'] != 0) {//已經激活過
            throw new UserException('ACCOUNT_ACTIVE_ALREADY_ACTIVE_ERROR');
        }

        $iTokenActive = 1;
        $iUserID = $aToken['id'];
        $_oUsersActiveToken::updateToken(['token_active' => $iTokenActive], ['token'=>$_sToken]);
        $_oUsers::updateById($iUserID, ['token_active' => $iTokenActive]);
        return ['result' => true, 'data' => []];
    }

    /**
     * 管理者更新使用者
     *
     * @param Request $_oRequest
     * @param Users $_oUsers
     * @return void
     */
    public function updateUserByAdmin($_iAccountId, Request $_oRequest, Users $_oUsers)
    {
        $aFieldsCondition = $_oRequest->only([
            //只允許他改以下參數
            'hallid',
            'account',
            'nickname',
            'password',
            'mobile',
            'email',
            'memo',
            'token_active',
            'active',
        ]);

        if(empty($aFieldsCondition)) {
            throw new UserException('ACCOUNT_DATA_UPDATE_EMPTY');
        }

        if(isset($aFieldsCondition['password'])) { //密碼需加密
            $aFieldsCondition['password'] = Hash::make($aFieldsCondition['password']);
        }

        $_oUsers::updateById($_iAccountId, $aFieldsCondition);
        return ['result' => true, 'data' => []];
    }

    /**
     * 使用者更改自身
     *
     * @param Request $_oRequest
     * @param Users $_oUsers
     * @return void
     */
    public function updateSelf(Request $_oRequest, Users $_oUsers)
    {
        $aFieldsCondition = $_oRequest->only([
            //只允許他改以下參數
            'password',
            'nickname',
            'mobile',
            'email',
        ]);

        if(empty($aFieldsCondition)) {
            throw new UserException('ACCOUNT_DATA_UPDATE_EMPTY');
        }

        if(isset($aFieldsCondition['password'])) { //密碼需加密
            $aFieldsCondition['password'] = Hash::make($aFieldsCondition['password']);
        }
        $aUser = auth()->user()->toArray();
        $_oUsers::updateById($aUser['id'], $aFieldsCondition);
        return ['result' => true, 'data' => []];
    }

    /**
     * 使用者登入.
     */
    public function login(Request $_oRequest, Users $_oUsers)
    {
        $aCredentials['account'] = $_oRequest->input('account');//帳號
        $aCredentials['password'] = $_oRequest->input('password');//密碼
        $aCredentials['active'] = 1; //啟用
        $aCredentials['hallid'] = 1; //圓夢

        //先驗證帳密
        if (!$sToken = auth()->attempt($aCredentials)) {
            throw new UserException('ACCOUNT_OR_PASSWORD_ERROR');
        }
       
        //後驗證是否有激活
        $aCredentials['token_active'] = 1; //已激活
        if (!$sToken = auth()->attempt($aCredentials)) {
            throw new UserException('ACCOUNT_NOT_ACTIVE_ERROR');
        }

        $aUser = auth()->user()->toArray();
        $_oUsers::updateById($aUser['id'], ['token' => $sToken]);
        $aUser = collect($aUser)->only(['account', 'nickname', 'mobile', 'email']);
        return [
            'result' => true, 
            'data' => [
                'token' => $sToken,
                'user' => $aUser,
            ]
        ];
    }
    
    public function logout(Request $_oRequest)
    {
        auth()->logout();
        return ['result' => true, 'data' => []];
    }

    /**
     * 用id取得使用者資料
     *
     * @param [int] $_iId 使用者id
     * @return 使用者資料
     */
    public function getUserById($_iAccountId, Users $_oUsers)
    {
        $aUser = $_oUsers::getUserById($_iAccountId);
        return ['result' => true, 'data' => $aUser];
    }

    public function getOrder($_iAccountId, ProductOrderList $_oProductOrderList)
    {
        $aData = $_oProductOrderList::getList(['account_id' => $_iAccountId], []);
        return ['result' => true, 'data' => $aData];
    }

    /**
     * 取得使用者常用地址
     *
     * @param [type] $_iAccountId
     * @param UserUsualAddress $_oUserUsualAddress
     * @return void
     */
    public function getUsualAddress($_iAccountId, UserUsualAddress $_oUserUsualAddress)
    {
        $aData = $_oUserUsualAddress::getByAccountId($_iAccountId, ['id', 'name', 'mobile', 'convenience_store', 'address']);
        return ['result' => true, 'data' => $aData];
    }

    /**
     * 新增常用地址
     *
     * @param Request $_oRequest
     * @param [type] $_iAccountId
     * @param UserUsualAddress $_oUserUsualAddress
     * @return void
     */
    public function addUsualAddress(Request $_oRequest, $_iAccountId, UserUsualAddress $_oUserUsualAddress)
    {
        $sAddress = $_oRequest->input('address'); //地址
        $sConvenienceStore = $_oRequest->input('convenience_store', ''); //超商
        $sName = $_oRequest->input('name'); //寄件人
        $sMobile = $_oRequest->input('mobile'); //寄件聯絡電話
        $_oUserUsualAddress::add($_iAccountId, $sAddress, $sConvenienceStore, $sName, $sMobile);
        return ['result' => true, 'data' => []];
    }

    /**
     * 更新常用地址
     *
     * @param Request $_oRequest
     * @param [type] $_iId
     * @param UserUsualAddress $_oUserUsualAddress
     * @return void
     */
    public function modifyUsualAddress(Request $_oRequest, $_iId, UserUsualAddress $_oUserUsualAddress)
    {
        $sAddress = $_oRequest->input('address', null); //地址
        $sConvenienceStore = $_oRequest->input('convenience_store', null); //超商
        $sName = $_oRequest->input('name', null); //寄件人
        $sMobile = $_oRequest->input('mobile', null); //寄件聯絡電話
        
        if(empty([$sAddress, $sConvenienceStore])) {
            throw new UserException('ACCOUNT_USUAL_ADDRESS_UPDATE_EMPTY');
        }

        $aFieldsConditionTmp = [
            'address' => $sAddress,
            'convenience_store' => $sConvenienceStore,
            'name' => $sName,
            'mobile' => $sMobile,
        ];
        $aFieldsCondition = [];

        foreach($aFieldsConditionTmp as $sCol => $sVal) {
            if($sVal === null) {
                continue;
            }
            $aFieldsCondition[$sCol] = $sVal;
        }

        $_oUserUsualAddress::modifyById($_iId, $aFieldsCondition);
        return ['result' => true, 'data' => []];
    }

    public function delectUsualAddress($_iId, UserUsualAddress $_oUserUsualAddress)
    {
        UserUsualAddress::delectById($_iId);
        return ['result' => true, 'data' => []];
    }

    /**
     * 取得使用者list
     *
     * @param Request $_oRequest
     * @param Users $_oUsers
     * @return void
     */
    public function getUserList(Request $_oRequest, Users $_oUsers){
        $iAccountId = $_oRequest->input('account_id', null); //使用者ID
        $iActive = $_oRequest->input('active', null); //狀態 -1停用, 1啟用
        $iHallId = $_oRequest->input('hallid', null); //平台id
        $iTokenActive = $_oRequest->input('token_active', null); //激活 0未激活, 1已激活
        $sAccount = $_oRequest->input('account', null); //帳號
        $sNickname = $_oRequest->input('nickname', null); //暱稱
        $sMobile = $_oRequest->input('mobile', null); //電話
        $sEmail = $_oRequest->input('email', null); //email
        $dStart = $_oRequest->input('start_date', null); //更新開始時間
        $dEnd = $_oRequest->input('end_date', null); //更新結束時間
        $sOrderBy = $_oRequest->input('order_by', 'id'); //排序欄位
        $sOrderType = $_oRequest->input('order_type', 'desc'); //排序方式 升序或降序
        $iLimit = $_oRequest->input('limit', 20); //一頁筆數

        $aFieldsConditionTmp = [
            'id' => $iAccountId,
            'active' => $iActive,
            'token_active' => $iTokenActive,
            'hallid' => $iHallId,
            'account' => $sAccount,
            'nickname' => $sNickname,
            'mobile' => $sMobile,
            'email' => $sEmail,
        ];
        $aFieldsCondition = [];

        foreach($aFieldsConditionTmp as $sCol => $sVal) {
            if($sVal === null) {
                continue;
            }
            $aFieldsCondition[$sCol] = $sVal;
        }

        $aDate = [];
        if($dStart !== null) {
            if($dEnd === null) { //開始跟結束日缺一不可
                throw new UserException('ACCOUNT_SEARCH_DATE_TIME_ERROR');
            }

            $aDate = [$dStart, $dEnd];
        }

        $aData = $_oUsers::getList([], $aFieldsCondition, $sOrderBy, $sOrderType, $iLimit, $aDate);
        return ['result' => true, 'data' => $aData];
    }

    /**
     *  取得目前操作者資訊
     *
     * @param Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getSelf(Request $_oRequest)
    {
        $aUser = auth()->user()->toArray();
        $aUser = collect($aUser)->only(['account', 'nickname', 'mobile', 'email']);
        return ['result' => true, 'data' => $aUser];
    }

    /**
     * 使用token重置驗證碼
     *
     * @param [type] $_sToken
     * @param Request $_oRequest
     * @return void
     */
    public function resetAccount(
        $_sToken, 
        Request $_oRequest,
        UsersActiveToken $_oUsersActiveToken,
        Users $_oUsers
    )
    {
        $sPassword = $_oRequest->input('password');
        $aToken = $_oUsersActiveToken::getByToken($_sToken);
        $iNow = strtotime(Carbon::now()->toDateTimeString());

        if(empty($aToken)) { //驗證碼不存在
            throw new UserException('ACCOUNT_ACTIVE_TOKEN_EMPTY');
        }
        
        if($iNow > strtotime($aToken['deadline'])) { //超過驗證時間
            throw new UserException('ACCOUNT_ACTIVE_DEADLINE_ERROR');
        }

        if($aToken['token_active'] != 0) {//已經激活過
            throw new UserException('ACCOUNT_ACTIVE_ALREADY_ACTIVE_ERROR');
        }

        $iTokenActive = 1;
        $iUserID = $aToken['id'];
        $_oUsersActiveToken::updateToken(['token_active' => $iTokenActive], ['token'=>$_sToken]);
        $_oUsers::updateById($iUserID, ['password' => Hash::make($sPassword)]);
        return ['result' => true, 'data' => []];
    }

    /**
     * 產生忘記密碼信件
     *
     * @param Request $_oRequest
     * @param Users $_oUsers
     * @param UsersActiveToken $_oUsersActiveToken
     * @return void
     */
    public function createResetAccount(
        Request $_oRequest, 
        Users $_oUsers, 
        UsersActiveToken $_oUsersActiveToken
    )
    {
        $sEmail = $_oRequest->input('email'); //要重置密碼電子信箱
        $iHallId = 1; //購物網ID
        $sTokenActive = 0; //mail認證改回未認證
        
        //用mail取得使用者資訊
        $aUser = $_oUsers::getList(['id', 'account'], ['email'=>$sEmail, 'hallid'=>$iHallId]);
        if(empty($aUser)) {
            throw new UserException('ACCOUNT_RESET_EMAIL_EMPTY');
        }
        $aUser = $aUser[0];
        
        //把使用者改為未認證
        $_oUsers::updateById($aUser['id'], ['token_active'=>$sTokenActive]);

        //產生token
        $iDeadlineDays = 1;//1天
        $sToken = Hash::make($sEmail.$iHallId);
        $sToken = str_replace('/', '', $sToken);//去掉斜線，不然客端連結會有問題
        $_oUsersActiveToken::add($aUser['id'], $sToken, $iDeadlineDays);
        
        $sDomain = 'http://dream-wr.net/member/reset?token='.$sToken;
        $aViewData = [
            'domain' => $sDomain,
            'account' => $aUser['account'],
        ];

        //寄信
        $this->sendMail (
            $sEmail, 
            '圓夢募資 會員密碼變更通知', 
            'emails.user_forgot', 
            $aViewData
        );
        return ['result' => true, 'data' => []];
    }

    private function sendMail($_sEmail, $_sSubject, $_sView, $_aViewData)
    {    
        $sFrom = 'teamdream168@gmail.com';
        $sName = '圓夢募資';
        Mail::send($_sView, $_aViewData, function ($oMessage) use ($sFrom, $sName, $_sEmail, $_sSubject) {
            $oMessage->from($sFrom, $sName)->to($_sEmail)->subject($_sSubject);
        });
    }
}
