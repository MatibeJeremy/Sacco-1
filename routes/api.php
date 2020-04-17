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

Route::group(['namespace' => 'Api\V1', 'prefix' => 'v1'], function () {

    // authentication routes
    Route::group(['prefix' => 'auth'], function () {
        Route::post('register', 'AuthController@register');
        Route::post('login', 'AuthController@login');
        Route::post('logout', 'AuthController@logout');
    });

    // all routes that require jwt verification
    Route::group(['middleware' => ['jwt.verify']], function (){
        // accounts routes
        Route::group(['prefix' => 'accounts'], function() {
            Route::get('/', 'AccountController@index');
            Route::get('/{id}', 'AccountController@show');
            Route::put('/{id}', 'AccountController@update');
        });

        // transactions routes
        Route::group(['prefix' => 'transactions'], function() {
            Route::get('/', 'TransactionController@index');
            Route::get('/{id}', 'TransactionController@show');
            Route::post('/', 'TransactionController@store');
            Route::put('/{id}', 'TransactionController@update');
        });

        // users routes
        Route::group(['prefix' => 'users'], function() {
            Route::get('/', 'UserController@index');
            Route::get('/{id}', 'UserController@show');
            Route::post('/', 'UserController@store');
            Route::put('/{id}', 'UserController@update');
        });
    });
});
