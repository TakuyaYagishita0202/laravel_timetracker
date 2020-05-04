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
Route::get('/projects', 'ProjectController@index');
Route::post('/projects', 'ProjectController@store');
Route::post('/projects/{id}/timers/stop', 'TimerController@stopRunning');
Route::post('/projects/{id}/timers', 'TimerController@store');
Route::get('/project/timers/active', 'TimerController@running');
Route::get('/timers', 'TimerController@index');
Route::post('/timers', 'TimerController@store');
Route::get('/categories', 'CategoryController@index');
Route::post('/categories', 'CategoryController@store');