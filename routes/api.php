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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::group([], function(){

    Route::post('user-register', 'App\Http\Controllers\AuthController@userRegister');
    Route::post('code-verify', 'App\Http\Controllers\AuthController@codeVerification');
    Route::post('register', 'App\Http\Controllers\AuthController@register');
    Route::post('login', 'App\Http\Controllers\AuthController@login');

    Route::get('unauth', 'App\Http\Controllers\AuthController@unAuthMessage')->name('unauth');

    Route::group(['middleware'=>'auth:api'], function(){
        Route::get('logout', 'Auth\AuthController@logout');//logout

        Route::post('profile-update', 'App\Http\Controllers\ProfileController@updateProfile');
        Route::group(['middleware'=>'admin'], function(){
            Route::post('send-mail', 'App\Http\Controllers\AuthController@sendMail');
        });

    });
});
