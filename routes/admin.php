<?php

Route::group([
    'namespace'  => 'App\Http\Controllers\Admin',
    'prefix'     => 'admin',
    'middleware' => ['auth'],
], function () {
    Route::resource('role', 'RoleController');
    Route::resource('permission', 'PermissionController');
});
