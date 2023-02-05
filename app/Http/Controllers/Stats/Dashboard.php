<?php

namespace App\Http\Controllers\Stats;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Product\ProductOrderList;
use App\Model\Product\ProductList;
use App\Model\User\Users;


class Dashboard extends Controller
{
    public function getHome($_iHallId, ProductOrderList $_oProductOrderList, Users $_oUsers, ProductList $_oProductList)
    {
        $aUser = $_oUsers::getList(['id'], ['hallid' => $_iHallId]);
        $aUser = collect($aUser)->pluck('id')->all();
        $aData['product_list_status_1_count'] = count($_oProductList::getList(['id'], ['status'=>1]));

        //付款狀態 0未付款, 1已付款
        $aData['pay_status_0_count'] = count($_oProductOrderList::getList(['account_id'=>$aUser, 'pay_status'=>0, 'order_status'=>0], ['pay_status']));

        //訂單狀態 0審核中, 1訂單通過, -1訂單取消
        $aData['order_status_0_count'] = count($_oProductOrderList::getList(['account_id'=>$aUser, 'order_status'=>0], ['order_status']));

        //出貨狀態 0待出貨, 1已出貨, -1出貨取消; 『待出貨且訂單通過』
        $aData['delivery_status_0_count'] = count($_oProductOrderList::getList(['account_id'=>$aUser, 'delivery_status'=>0, 'order_status'=>1], ['delivery_status']));

        $aData['user_count'] = count($aUser);

        return ['result' => true, 'data' => $aData];
    }
}
