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
Route::post('register', 'App\Http\Controllers\AuthController@register');
Route::post('login', 'App\Http\Controllers\AuthController@login');
Route::group([], function(){

//    Route::post('register', 'App\Http\Controllers\AuthController@register');
//    Route::post('login', 'App\Http\Controllers\AuthController@login');
    Route::get('unauth', 'Auth\AuthController@unAuthMessage')->name('unauth');

    Route::group(['middleware'=>'auth:api'], function(){
        Route::get('logout', 'Auth\AuthController@logout');//logout

        Route::group(['middleware'=>'admin'], function(){
            //course
//            Route::get('course', 'Course\CourseController@index');
        });
    });
});
