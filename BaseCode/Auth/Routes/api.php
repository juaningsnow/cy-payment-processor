<?php

Route::prefix('auth')->group(function () {
    Route::post('login', 'LoginController@login')->name('login');
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', 'LoginController@logout');
        Route::get('user', 'AuthController@user');
    });
});

Route::prefix('permissions')->group(function () {
    // Route::get('{permissionId}', 'PermissionApiController@show');
    Route::get('/', 'PermissionApiController@index');
    // Route::patch('{permissionId}', 'PermissionApiController@update');
    // Route::post('/', 'PermissionApiController@store');
    // Route::post('/multiple', 'PermissionApiController@destroyMultiple');
    // Route::delete('{permissionId}', 'PermissionApiController@destroy');
});

Route::prefix('roles')->group(function () {
    Route::get('/', 'RoleApiController@index');
    Route::get('/export', 'RoleApiController@export');
    Route::patch('{roleId}', 'RoleApiController@update');
    Route::post('/', 'RoleApiController@store');
    Route::post('/multiple', 'RoleApiController@destroyMultiple');
    Route::get('{roleId}', 'RoleApiController@show');
    Route::delete('{roleId}', 'RoleApiController@destroy');
});

Route::prefix('users')->group(function () {
    Route::get('/', 'UserApiController@index');
    Route::get('/export', 'UserApiController@export');
    Route::get('{userId}', 'UserApiController@show');
    Route::patch('{userId}/password', 'UserApiController@updatePassword');
    Route::patch('{userId}/settings', 'UserApiController@updateSettings');
    Route::patch('{userId}/profile', 'UserApiController@updateProfile');
    Route::patch('{userId}', 'UserApiController@update');
    Route::post('/', 'UserApiController@store');
    Route::delete('{userId}', 'UserApiController@destroy');
});
