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
Route::redirect('/', '/home');
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/timers', 'TimerController@index');
Route::post('/timers', 'TimerController@store');
Route::post('/timers/{id}/update', 'TimerController@update');
Route::delete('/timers/{id}/delete', 'TimerController@delete');
Route::post('/timers/stop', 'TimerController@stopRunning');
Route::get('/timers/active', 'TimerController@running');
Route::get('/categories', 'CategoryController@index');
Route::post('/categories', 'CategoryController@store');