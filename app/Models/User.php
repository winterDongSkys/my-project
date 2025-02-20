<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use HasFactory,SoftDeletes;

    // 定义用户表名
    protected $table = 'users';

    // 定义可批量赋值的字段
    protected $fillable = [
        'name',
        'email',
        'subscription_id', // 订阅ID
        'account_id',      // 用户关联id
        'subscription_status', // 订阅状态
        'subscription_expiry' // 订阅到期时间
    ];

    protected $dates = ['deleted_at'];

    // 关系：一个用户可能有多个订单
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
