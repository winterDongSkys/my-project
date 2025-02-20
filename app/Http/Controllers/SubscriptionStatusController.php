<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionStatusController extends Controller
{
    /**
     * @description 获取订阅状态
     * @param Request $request
     * @return JsonResponse
     * @author: ZD
     *
     */
    public function getSubscriptionStatus(Request $request):JsonResponse
    {
        $data = $request->validate(
            ['id' => 'required|integer'],
            ['id.required' => '用户 ID 为空', 'id.integer' => 'ID 类型错误']
        );

        $user = User::query()->find($data['id']);
        if(empty($user)) {
            return response()->json(['code' => 5003, 'msg' => '数据不存在']);
        }

        //todo 订阅过期Google主动推送还是系统有监听任务，如果状态不是实时查询未了保证数据准确性可以再主动获取Google API调用订阅状态查询
        //一般系统应该会有监控系统实时修改状态，这里只是查询系统数据库中的状态返回
        return response()->json([
            'code'=>200,
            'user_id' => $user->id,
            'subscription_status' => $user->subscription_status,
            'subscription_status_msg' => config('subscription.status_msg')[$user->subscription_status],//订阅状态文字描述
            'subscription_expiry' => $user->subscription_expiry,
        ]);
    }
}
