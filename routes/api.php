<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// 会員登録
Route::post('/register', 'Auth\RegisterController@register')->name('register');
// ログイン
Route::post('/login', 'Auth\LoginController@login')->name('login');
// ログアウト
Route::post('/logout', 'Auth\LoginController@logout')->name('logout');
// ログインユーザーのチェック
Route::get('/user', function () {
    return Auth::user();
})->name('user');

// 記録を一覧で取得
Route::get('timers', 'TimerController@index');
// タイマー開始と同時に記録を保存
Route::post('timers', 'TimerController@store');
// 記録をマニュアルで保存
Route::post('timers/save', 'TimerController@save');
// 記録を更新
Route::put('timers/{id}', 'TimerController@update');
// 記録を削除
Route::delete('timers/{id}', 'TimerController@delete');
// タイマーを停止
Route::post('timers/stop', 'TimerController@stopRunning');
// アクティブなタイマーを取得
Route::get('timers/active', 'TimerController@running');
// カテゴリーを作成
Route::post('categories', 'CategoryController@store');
// カテゴリーを一覧で取得
Route::get('categories', 'CategoryController@index');