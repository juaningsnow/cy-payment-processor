<?php

namespace BaseCode\Auth\Routes;

use Illuminate\Support\Facades\Route;

class AuthRoutes
{
    public static function api()
    {
        Route::namespace('\BaseCode\Auth\Controllers')->prefix('auth')->group(function () {
            Route::post('login', 'LoginController@login')->name('login');
            Route::middleware('auth:sanctum')->group(function () {
                Route::post('logout', 'LoginController@logout');
                Route::get('user', 'AuthController@user');
            });
        });
    }
}
