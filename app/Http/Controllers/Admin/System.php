<?php

namespace App\Http\Controllers\Admin;

use Hash;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\CodeError\Admin\SystemException;
use App\Model\Admin\AdminUser;

class System extends Controller
{
    const VALIDATOR_RULE = [
        'account'  => 'regex:/^[A-Za-z0-9]+$/|alpha_num|between:4,12',
        'password' => 'alpha_num|between:8,12',
    ];

    /**
     * 後台帳號登入驗證
     *
     * @param Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function login(Request $request)
    {
        // 參數驗證
        $validator = Validator::make($request->all(), [
            'account'  => 'required|' . self::VALIDATOR_RULE['account'],
            'password' => 'required|' . self::VALIDATOR_RULE['password'],
        ]);
        if ($validator->fails()) {
            throw new SystemException('ACCOUNT_OR_PASSWORD_ERROR');
        }

        // 登入驗證
        $request['active'] = 1;
        $credentials = request(['account', 'password', 'active']);
        if (! $token = auth()->attempt($credentials)) {
            throw new SystemException('AUTHENTICATION_FAILED');
        }

        // 更新帳號相關資訊
        $user = auth()->user();
        AdminUser::find($user->id)->update([
            'token'    => $token,
            'login_at' => \Carbon\Carbon::now(),
        ]);

        return response()->json([
            'result' => true,
            'data' => [
                'token' => $token,
                'user'  => [
                    'account' => $user->account,
                    'name'    => $user->name,
                ],
            ]
        ]);
    }

    /**
     *  取得目前操作者資訊
     *
     * @param Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function information(Request $request)
    {
        try {
            $user = auth()->user();
            return response()->json([
                'result' => true,
                'data'   => [
                    'account' => $user->account,
                    'name'    => $user->name,
                ],
            ]);
        } catch (\Exception $e) {
            throw new SystemException('INTERNAL_SERVER_ERROR', ['message' => $e->getMessage()]);
        }
    }

    /**
     *  修改個人密碼
     *
     * @param Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updatePassword(Request $request)
    {
        // 參數驗證
        $validator = Validator::make($request->all(), [
            'old_password' => 'required|' . self::VALIDATOR_RULE['password'],
            'password'     => 'required|confirmed|' . self::VALIDATOR_RULE['password'],
        ]);
        if ($validator->fails()) {
            throw new SystemException('PARAMETER_INCORRECT');
        }

        $user = auth()->user();
        // 檢查原密碼是否正確
        if (!Hash::check($request->input('old_password'), $user->password)) {
            throw new SystemException('OLD_PASSWORD_INCORRECT');
        }

        // 更新密碼並且自動登出
        AdminUser::find($user->id)->update([
            'password' => Hash::make($request->input('password')),
            'token'    => '',
            'login_at' => \Carbon\Carbon::now(),
        ]);
        auth()->logout();

        return response()->json(['result' => true, 'data' => []]);
    }

    /**
     *  登出
     *
     * @param Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function logout(Request $request)
    {
        try {
            auth()->logout();
            return response()->json(['result' => true, 'data' => []]);
        } catch (\Exception $e) {
            throw new SystemException('INTERNAL_SERVER_ERROR', ['message' => $e->getMessage()]);
        }
    }
}
