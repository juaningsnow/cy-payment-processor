<?php
namespace BaseCode\Auth\Routes;

use Illuminate\Support\Facades\Route;

class UserRoutes
{
    public static function api()
    {
        Route::prefix('user-management')->group(function () {
            Route::get('/', '\BaseCode\Auth\Controllers\UserApiController@index');
            Route::get('/export', '\BaseCode\Auth\Controllers\UserApiController@export');
            Route::get('{userId}', '\BaseCode\Auth\Controllers\UserApiController@show');
            Route::patch('{userId}/password', '\BaseCode\Auth\Controllers\UserApiController@updatePassword');
            Route::patch('{userId}/settings', '\BaseCode\Auth\Controllers\UserApiController@updateSettings');
            Route::patch('{userId}/profile', '\BaseCode\Auth\Controllers\UserApiController@updateProfile');
            Route::patch('{userId}', '\BaseCode\Auth\Controllers\UserApiController@update');
            Route::post('/', '\BaseCode\Auth\Controllers\UserApiController@store');
            Route::delete('{userId}', '\BaseCode\Auth\Controllers\UserApiController@destroy');
        });
    }
}
