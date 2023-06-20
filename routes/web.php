<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
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
Route::get('/','App\Http\Controllers\ServiceController@welcome')
->name('welcome');

Route::post('search','App\Http\Controllers\ServiceController@search');

Route::post('order','App\Http\Controllers\ServiceController@order');

Route::get('orders/get','App\Http\Controllers\ServiceController@checkOrders');

// Роуты AuthController

Route::post('/auth','App\Http\Controllers\AuthController@auth')
->middleware('guest')
->name('auth');

Route::get('profile','App\Http\Controllers\AuthController@profile')
->middleware('auth')
->name('profile');

Route::post('add/user','App\Http\Controllers\AuthController@addUser')
->name('add.user');

Route::get('logout',function(){
    Auth::logout();
    return redirect()->route('welcome');
});

Route::get('mail','App\Http\Controllers\ServiceController@sendMail');


// Роуты ajax

Route::get('/load/{value}','App\Http\Controllers\AjaxController@loadFile')
->where('value', '[[A-Za-z]+')
->where('value', '[0-9]+')
->where('value', '.*');

Route::post('/disable/order','App\Http\Controllers\AjaxController@disableOrder')
->middleware('auth');

Route::post('/service/find','App\Http\Controllers\AjaxController@findService')
->middleware('auth');

Route::post('/update/service','App\Http\Controllers\AjaxController@updateService')
->middleware('auth');

Route::post('order/sum','App\Http\Controllers\AjaxController@totalSum');

Route::post('/change/image','App\Http\Controllers\AjaxController@getUrlImage');

Route::post('/save/image','App\Http\Controllers\AjaxController@saveTemporaryImage');

Route::get('/crop','App\Http\Controllers\AjaxController@cropImage');

Route::get('/clear',function(){
    Artisan::call('cache:clear');
    Artisan::call('config:cache');
    Artisan::call('storage:link');
});
