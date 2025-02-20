

## 1.环境配置

- Laravel 10.
- Mysql 8.0.
- PHP 8.2.
- Apache 2.4.


## 2.项目启动

- **执行 `Composer install`**
- **复制`.env.example`文件创建 `.env`**
- **修改 `.env`中数据库账号密码**
- **数据迁移生成数据表 `php artisan migrate`**
- **配置域名访问或本地启动 `php artisan serve`**
- **查看数据表是否创建**



## 3.主要对外接口

### 3.1 模拟订阅通知接口

a) 接口地址 

域名/api/subscription-notification

b) 参数如下
```json
{
  "version":"1.0",
  "packageName":"com.some.thing",
  "eventTimeMillis":"1503349566168",
  "subscriptionNotification":
  {
    "version":"1.0",
    "notificationType":4,
    "purchaseToken":"PURCHASE_TOKEN",
    "subscriptionId":"monthly001"
  }
}
```
c)返回
```json
{
    "code": 200,
    "msg": "Notification handled successfully"
}
```
### 3.2 获取订阅状态接口

a) 接口地址

域名/api/subscription-status?id=1

b) 参数如下

参数 id 为 User 用户表主键ID

c)返回
```json
{
    "code": 200,
    "user_id": 7,
    "subscription_status": "paused",   
    "subscription_status_msg": "订阅已暂停",
    "subscription_expiry": "2025-03-20 13:02:13"
}
```
## 4.主要代码逻辑地址

1. 接收订阅推送控制器;

   `App\Http\Controllers\SubscriptionNotificationController`

2. 处理订阅推送逻辑层;

   `App\Services\SubscriptionService`

3. 获取用户订阅状态控制器;

   `App\Http\Controllers\SubscriptionStatusController` 

4. Google订阅状态与本系统状态转换配置。

   `config\subscription.php`

5. 路由配置地址。

   `routes\api.php`

6. 日志存储地址

   `storage\logs\laravel.log`
