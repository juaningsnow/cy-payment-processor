<?php
namespace BaseCode\Auth\Routes;

use Illuminate\Support\Facades\Route;

class RoleRoutes
{
    public static function api()
    {
        Route::prefix('roles')->group(function () {
            Route::get('/', '\BaseCode\Auth\Controllers\RoleApiController@index');
            Route::get('/export', '\BaseCode\Auth\Controllers\RoleApiController@export');
            Route::patch('{roleId}', '\BaseCode\Auth\Controllers\RoleApiController@update');
            Route::post('/', '\BaseCode\Auth\Controllers\RoleApiController@store');
            Route::post('/multiple', '\BaseCode\Auth\Controllers\RoleApiController@destroyMultiple');
            Route::get('{roleId}', '\BaseCode\Auth\Controllers\RoleApiController@show');
            Route::delete('{roleId}', '\BaseCode\Auth\Controllers\RoleApiController@destroy');
        });
    }
}
