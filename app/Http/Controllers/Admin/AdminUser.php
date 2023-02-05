<?php

namespace App\Http\Controllers\Admin;

use DB;
use Hash;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Exceptions\CodeError\Admin\AdminUserException;
use App\Model\Admin\AdminUser as AdminUserModel;

class AdminUser extends Controller
{
    const VALIDATOR_RULE = [
        'id'       => 'integer|exists:admin_user,id',
        'account'  => 'regex:/^[A-Za-z0-9]+$/|alpha_num|between:4,12',
        'password' => 'alpha_num|between:8,12',
        'name'     => 'max:10',
        'active'   => 'in:1,2',
    ];

    /**
     * 列表
     *
     * @param Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request)
    {
        $query = AdminUserModel::select(['id', 'account', 'name', 'login_at', 'created_at', 'active', 'updated_at']);
        $account = $request->input('account', '');
        if ($account != '') {
            $query->where('account', 'LIKE', '%'. $account. '%');
        }
        $active = $request->input('active', '');
        if (in_array($active, ['1', '2'])) {
            $query->where('active', $active);
        }
        $list = $query->orderBy('login_at', 'DESC')->paginate(config('common.admin.paginate'));

        return response()->json(['result' => true, 'data' => $list]);
    }

    /**
     *  新增
     *
     * @param Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function store(Request $request)
    {
        // 參數驗證
        $validator = Validator::make($request->all(), [
            'account'  => 'required|' . self::VALIDATOR_RULE['account'],
            'password' => 'required|confirmed|' . self::VALIDATOR_RULE['password'],
            'name'     => 'required|' . self::VALIDATOR_RULE['name'],
        ]);
        if ($validator->fails()) {
            throw new AdminUserException('PARAMETER_INCORRECT');
        }
        $request = $request->all();

        // 檢查帳號是否已存在
        if (AdminUserModel::select('id')->where('account', $request['account'])->count() > 0) {
            throw new AdminUserException('ACCOUNT_DUPLICATE');
        }

        // 新增帳號資料
        $nowAt = \Carbon\Carbon::now();
        $adminUser = AdminUserModel::create([
            'account'    => $request['account'],
            'password'   => Hash::make($request['password']),
            'name'       => $request['name'],
            'token'      => '',
            'created_at' => $nowAt,
            'updated_at' => $nowAt,
        ]);

        return response()->json(['result' => true, 'data' => $adminUser]);
    }

    /**
     *  修改
     *
     * @param Request  $request
     * @param integer  $id      [ref: admin_user > id]
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function update(Request $request, $id)
    {
        // 參數驗證
        $request['id'] = $id;
        $validator = Validator::make($request->all(), [
            'id'       => 'required|' . self::VALIDATOR_RULE['id'],
            'password' => 'confirmed|' . self::VALIDATOR_RULE['password'],
            'name'     => 'required|' . self::VALIDATOR_RULE['name'],
            'active'   => 'required|' . self::VALIDATOR_RULE['active'],
        ]);
        if ($validator->fails()) {
            throw new AdminUserException('PARAMETER_INCORRECT');
        }
        $request = $request->all();

        // 更新帳號資料
        DB::transaction(function () use ($request) {
            $adminUser = AdminUserModel::find($request['id']);
            $kickUser = false;
            $nowAt = \Carbon\Carbon::now();
            $parameter = ['name' => $request['name'], 'active' => $request['active'], 'updated_at' => $nowAt];
            if (isset($request['password']) && $request['password'] != '') {
                $parameter['password'] = Hash::make($request['password']);
                $kickUser = true;
            }
            if ($kickUser === true || ($adminUser->active == 1 && $request['active'] == 2)) { // 修改密碼或停用一律進行踢除動作
                $parameter['token'] = '';
                $this->setTokenInvalidate($adminUser->id, $adminUser->token);
            }
            $adminUser->update($parameter);
        });

        return response()->json(['result' => true, 'data' => []]);
    }

    /**
     *  刪除
     *
     * @param Request  $request
     * @param integer  $id      [ref: admin_user > id]
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function destroy(Request $request, $id)
    {
        // 參數驗證
        $request['id'] = $id;
        $validator = Validator::make($request->all(), ['id' => 'required|' . self::VALIDATOR_RULE['id']]);
        if ($validator->fails()) {
            throw new AdminUserException('PARAMETER_INCORRECT');
        }

        // 刪除帳號
        DB::transaction(function () use ($id) {
            $adminUser = AdminUserModel::find($id);
            $this->setTokenInvalidate($adminUser->id, $adminUser->token);
            $adminUser->delete();
        });

        return response()->json(['result' => true, 'data' => []]);
    }

    /**
     * 將 Token 進行失效動作
     *
     * @param integer $id    [ref: admin_user > id]
     * @param string  $token
     */
    public function setTokenInvalidate($id, $token)
    {
        try {
            auth()->setToken($token)->invalidate();
        } catch (\Exception $e) {
            AdminUserModel::find($id)->update(['token' => '']);
        }
    }
}
