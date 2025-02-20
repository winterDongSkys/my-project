<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory,SoftDeletes;

    // 定义产品表名
    protected $table = 'products';

    // 定义可批量赋值的字段
    protected $fillable = [
        'product_id',  // 产品 ID
        'product_name' // 产品名称
    ];

    protected $dates = ['deleted_at'];

    // 关系：一个产品可以有多个订单
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}

