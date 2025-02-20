<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('subscription_status')->default('');  // 用户订阅状态
            $table->string('account_id')->default('');  // 用户关联ID
            $table->string('subscription_id')->nullable(); // 订阅ID
            $table->timestamp('subscription_expiry')->nullable(); // 订阅到期时间
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
