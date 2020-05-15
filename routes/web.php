<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();
// Route::redirect('/', '/home');
// Route::get('/home', 'HomeController@index')->name('home');
Route::get('/api/timers', 'TimerController@index');
Route::post('/api/timers', 'TimerController@store');
Route::post('/api/timers/save', 'TimerController@save');
Route::put('/api/timers/{id}', 'TimerController@update');
Route::delete('/api/timers/{id}', 'TimerController@delete');
Route::post('/api/timers/stop', 'TimerController@stopRunning');
Route::get('/api/timers/active', 'TimerController@running');
Route::get('/api/categories', 'CategoryController@index');
Route::post('/api/categories', 'CategoryController@store');

// APIのURL以外のリクエストに対してはindexテンプレートを返す
Route::get('/{any}', function () {
    return view('index');
})->where('any', '.*');