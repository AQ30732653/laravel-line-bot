<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// 主動推送資料給 Line
Route::get('lineBot/push/{userId}/{content}', 'LineBotController@push');
// Line 推送資料給我方 (Bot Webhook URL)
Route::post('lineBot/callback', 'LineBotController@callback');
// Line 分享好友活動
Route::get('lineBot/shareActivity', 'LineBotController@shareActivity');

// 轉址到 Line 登入頁
Route::get('lineLogin', 'LineLoginController@index');
// Line 推送資料給我方 (第三方 Login)
Route::get('lineLogin/callback', 'LineLoginController@callback');
