<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class SubscriptionService
{

    const SUBSCRIPTION_RECOVERED  = 1; //从账号保留状态恢复了订阅
    const SUBSCRIPTION_RENEWED    = 2; //续订了处于活动状态的订阅
    const SUBSCRIPTION_PURCHASED  = 4; //购买了新的订阅

    /**
     * @description 创建或者修改用户信息
     * @param $params
     * @return int
     * @throws Exception
     */
    public static function saveUser($params):int
    {

        $model =User::query()
            ->updateOrCreate(['account_id'=>$params['account_id'] ?? 0],[
                'name'       =>  $params['name'] ?? '',
                'email'      =>  $params['email'] ?? self::randomEmail(),
                'subscription_status' => $params['subscription_status'] ?? '',
                'subscription_id'     => $params['subscription_id'] ?? '',
                'subscription_expiry' => $params['subscription_expiry']
            ]);

        return $model->id;

    }

    /**
     * @description 生成一个随机邮箱
     * @return string
     * @throws Exception
     */
    public static function randomEmail():string
    {
        // 生成随机用户名，长度为 5 到 10 个字符
        $username = Str::random(random_int(5, 10));

        // 定义常见的邮箱域名列表
        $domains = ['gmail.com', 'yahoo.com', 'hotmail.com', 'example.com'];

        // 随机选择一个域名
        $domain = $domains[array_rand($domains)];

        // 组合生成邮箱地址
        return $username . '@' . $domain;

    }

    /**
     * @description 添加或修改订单
     * @param int $userId
     * @param string $purchaseToken
     * @param array $items
     * @return bool
     */
    public static function saveOrder(int $userId, string $purchaseToken,array $items):bool
    {
        if(!empty($items)){

            foreach ($items as $param){
                $model =Order::query()
                    ->updateOrCreate([
                        'user_id'=>$userId,
                        'purchase_token'=>$purchaseToken,
                        'product_id' =>  $param['productId'],
                    ],[
                        'status'     => $param['expiryTime'] > now() ?'active':'expired',
                        'expiry_date'=> $param['expiryTime'] ?? '',
                    ]);
                if(!$model->id){
                    return false;
                }

            }
        }
       return true;

    }

    /**
     * @description 执行订阅相关逻辑
     * 第一步：判断是否是新订阅用户（如果是非新订阅用户需要具体业务处理逻辑，暂未处理）
     * 第二步：通过Google Play API 获取订阅详细信息
     * 第三步：操作用户表User数据,新建或者更新订阅状态
     * 第四步：根据lineItems字段返回的产品数据处理订单数据（Order表一个purchaseToken会对应多个产品）
     * 第五步：返回数据
     * todo notificationType其他状态暂未考虑，linkedPurchaseToken存在时候撤销权利逻辑待完善
     * @param $notification
     * @return array
     */
    public static function handleNotification($notification):array
    {

        $subscriptionNotification = $notification['subscriptionNotification'];
        $notificationType = $subscriptionNotification['notificationType'];
        $purchaseToken    = $subscriptionNotification['purchaseToken'];
        $subscriptionId   = $subscriptionNotification['subscriptionId'];

        try {
            //新的订阅
            switch ($notificationType) {

                case self::SUBSCRIPTION_RECOVERED:
                case self::SUBSCRIPTION_RENEWED:
                case self::SUBSCRIPTION_PURCHASED:
                    // 模拟通过 Google Play API 获取订阅详细信息
                    $subscriptionDetails = self::getSubscriptionDetails($purchaseToken);

                    //记录查询日志用于后面数据核对
                    Log::info('start get subscription notification info', ['data' => $subscriptionDetails]);
                    if(empty($subscriptionDetails)){
                        return ['code'=>200,'message' => 'Notification handled successfully'];
                    }
                    // 开始事务
                    DB::beginTransaction();
                    //组装用户数据
                    $accountId = $subscriptionDetails['externalAccountIdentifiers'] ? $subscriptionDetails['externalAccountIdentifiers']['obfuscatedExternalAccountId'] : '';
                    $param['account_id'] = $accountId;
                    $param['name']       = 'test';
                    $param['subscription_status'] = config('subscription.status_map')[$subscriptionDetails['subscriptionState']];
                    $param['subscription_id']     = $subscriptionId;
                    $param['subscription_expiry'] = now()->addMonth(); //todo 获取所有订单中最晚到期时间作为订阅到期时间这里临时写死
                    $userId = self::saveUser($param);

                    if(!$userId){
                        throw new Exception('修改用户失败#'.$accountId,5001);
                    }
                    //处理订单相关，这里考虑lineItems是数组对应多个产品的情况
                    //这里不确定续订以及订阅关闭但是订单未到期重新开启后Google是否重新生成订单，所以根据用户订阅详情里面所以订单商品进行遍历更新修改
                    if(!empty($subscriptionDetails['lineItems'])){

                        $resOrder = self::saveOrder($userId,$purchaseToken,$subscriptionDetails['lineItems']);
                        if(!$resOrder){
                            throw new Exception('添加订单失败#'.$userId,5002);
                        }
                    }

                    //todo 还需要考虑linkedPurchaseToken逻辑,撤消授予 linkedPurchaseToken 的权利，以确保不会针对同一购买交易向多个用户授予权利
                    //提交事务
                    DB::commit();
                    break;
                default:
                    //todo notificationType 其他状态暂未考虑，需根据具体业务逻辑处理
                    return ['code'=>200, 'msg'=>'Notification handled successfully'];
            }


        }catch (Exception $e) {
            // 发生错误时回滚事务
            DB::rollBack();
            // 记录错误日志
            Log::error('Error handling subscription notification', ['error' => $e->getMessage()]);
            return ['code'=>$e->getCode(),'msg'=>$e->getMessage()];
        }

        return ['code'=>200, 'msg'=>'Notification handled successfully'];
    }


    /**
     * @description 模拟接口 purchases.subscriptionsv2 返回数据
     * @param $purchaseToken
     * @return array
     */
    private static function getSubscriptionDetails($purchaseToken):array
    {
        // 模拟返回订阅详细信息
        return [
            'latestOrderId'=>'GB123456',
            'linkedPurchaseToken'=>$purchaseToken,
            'subscriptionState'=>'SUBSCRIPTION_STATE_ACTIVE', //模拟返回激活状态
            //'subscriptionState'=>'SUBSCRIPTION_STATE_PAUSED',
            'lineItems'=>[
                [
                    'productId'=>10,
                    'expiryTime'=>now()->addMonth(),//模拟一个月后过期
                ],
                [
                    'productId'=>12,
                    'expiryTime'=>now()->addMonth(),//模拟一个月后过期
                ]
            ],
            'externalAccountIdentifiers'=>['obfuscatedExternalAccountId' => rand(1, 100)], // 模拟用户 ID,
            //'externalAccountIdentifiers'=>['obfuscatedExternalAccountId' => 26], // 模拟用户 ID,
        ];
    }
}
