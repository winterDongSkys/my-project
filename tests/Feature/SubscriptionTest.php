<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @description: 模拟测试订阅
     * @return void
     */
    public function testSubscriptionNotificationHandling()
    {
        $notification = [
            "version" => "1.0",
            "packageName" => "com.some.thing",
            "eventTimeMillis" => "1503349566168",
            "subscriptionNotification" => [
                "version" => "1.0",
                "notificationType" => 4,
                "purchaseToken" => "PURCHASE_TOKEN",
                "subscriptionId" => "monthly001"
            ]
        ];

        $response = $this->postJson('/api/subscription-notification', $notification);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Notification handled successfully']);
    }

    /**
     * @description: 模拟获取用户订阅状态状态
     * @return void
     */
    public function testSubscriptionStatusQuery()
    {
        $response = $this->getJson('/api/subscription/status/1');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'user_id',
            'subscription_status',
            'subscription_status_msg',
            'subscription_expiry',
        ]);
    }
}
