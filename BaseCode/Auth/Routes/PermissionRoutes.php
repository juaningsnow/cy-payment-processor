<?php
namespace BaseCode\Auth\Routes;

use Illuminate\Support\Facades\Route;

class PermissionRoutes
{
    public static function api()
    {
        Route::prefix('permissions')->group(function () {
            // Route::get('{permissionId}', '\BaseCode\Auth\PermissionApiController@show');
            Route::get('/', '\BaseCode\Auth\Controllers\PermissionApiController@index');
            // Route::patch('{permissionId}', '\BaseCode\Auth\PermissionApiController@update');
            // Route::post('/', '\BaseCode\Auth\PermissionApiController@store');
            // Route::post('/multiple', '\BaseCode\Auth\PermissionApiController@destroyMultiple');
            // Route::delete('{permissionId}', '\BaseCode\Auth\PermissionApiController@destroy');
        });
    }
}
