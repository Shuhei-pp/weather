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

Route::get('/welcome', function () {
    return view('welcome');
});

// check: routeは->name()で名前をつけられる！　viewのURL修正も楽になるから修正しよう
Route::post('/register/pre_check', 'App\Http\Controllers\Auth\RegisterController@preCheck')->name('register.pre_check');

Route::get('/register/verify/{token}', 'App\Http\Controllers\Auth\RegisterController@showMyPage');

Route::get('/', [App\Http\Controllers\HomeController::class,'index']);
Route::get('/area/edit', [App\Http\Controllers\AreaController::class,'toEditAreaPage']);
Route::post('/area/edit', [App\Http\Controllers\AreaController::class,'editArea']);

use App\Http\Controllers\AreaController;
Route::get('/area/{area_zip}',[AreaController::class,'index']);


Auth::routes();

Route::get('/user', [App\Http\Controllers\UsersController::class, 'index'])->name('home');
