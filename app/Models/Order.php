<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory,SoftDeletes;

    // 定义订单表名
    protected $table = 'orders';

    // 定义可批量赋值的字段
    protected $fillable = [
        'user_id', // 用户 ID
        'product_id', // 产品 ID
        'purchase_token', // 购买令牌
        'status', // 订单状态
        'expiry_date', // 订阅过期时间
    ];

    protected $dates = ['deleted_at'];

    // 关系：一个订单属于一个用户
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 关系：一个订单对应一个产品
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

