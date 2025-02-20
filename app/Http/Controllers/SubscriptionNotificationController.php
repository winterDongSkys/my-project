<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\SubscriptionService;
use Illuminate\Support\Facades\Log;

class SubscriptionNotificationController extends Controller
{

    /**
     * @description 模拟接收订阅通知
     * @param Request $request
     * @return JsonResponse
     */
    public function handleNotification(Request $request): JsonResponse
    {
        $notification = $request->all();

        //将获取的接口数据写入日志
        Log::info('start subscription notification', ['data' => $notification]);

        if(isset($notification['subscriptionNotification'])){

            $res = SubscriptionService::handleNotification($notification);
            return response()->json($res);
        }

        return response()->json(['code'=>200,'message' => 'Notification handled successfully']);
    }



}
