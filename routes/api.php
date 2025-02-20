<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubscriptionNotificationController;
use App\Http\Controllers\SubscriptionStatusController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::post('/subscription-notification', [SubscriptionNotificationController::class, 'handleNotification']);//订阅通知
Route::get('/subscription-status', [SubscriptionStatusController::class, 'getSubscriptionStatus']);//订阅状态查询

