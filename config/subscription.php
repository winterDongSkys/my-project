<?php
return [
    'status_map' => [
        'SUBSCRIPTION_STATE_UNSPECIFIED' => 'unknown',//未指定订阅状态。canceled paused expired
        'SUBSCRIPTION_STATE_PENDING' => 'inactive',//订阅已在注册过程中创建，但正在等待付款。在此状态下，所有商品都在等待付款
        'SUBSCRIPTION_STATE_ACTIVE' => 'active',//订阅处于有效状态。- (1) 如果订阅是自动续订型方案，则至少一个商品为 autoRenewEnabled 且未过期。- (2) 如果订阅是预付费方案，则至少一个商品未过期。
        'SUBSCRIPTION_STATE_PAUSED' => 'paused',//订阅已暂停。仅当订阅是自动续订型方案时，此状态才适用。在此状态下，所有商品都处于暂停状态。
        'SUBSCRIPTION_STATE_IN_GRACE_PERIOD' => 'active',//订阅处于宽限期。仅当订阅是自动续订型方案时，此状态才适用。在此状态下，所有商品都处于宽限期。
        'SUBSCRIPTION_STATE_ON_HOLD' => 'canceled',//订阅已冻结（已中止）。仅当订阅是自动续订型方案时，此状态才适用。在此状态下，所有商品都处于冻结状态。
        'SUBSCRIPTION_STATE_CANCELED' => 'canceled',//订阅已取消，但尚未过期。仅当订阅是自动续订型方案时，此状态才适用。所有商品都将 autoRenewEnabled 设置为 false。
        'SUBSCRIPTION_STATE_EXPIRED' => 'expired',//订阅已过期。所有商品的 expiryTime 都为过去的时间。
        'SUBSCRIPTION_STATE_PENDING_PURCHASE_CANCELED' => 'inactive',//订阅的待处理交易已取消。如果相应待处理的购买交易涉及某项现有订阅，请使用 linkedPurchaseToken 获取该订阅的当前状态。
    ],
    'status_msg'=>[
        'unknown'=>'订阅状态未知',
        'inactive'=>'订阅未激活',
        'active'=>'订阅已激活',
        'paused'=>'订阅已暂停',
        'canceled'=>'订阅已取消',
        'expired'=>'订阅过期',
    ]
];
