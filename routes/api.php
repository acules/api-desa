<?php

// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [App\Http\Controllers\Api\Auth\LoginController::class, 'index']);

//group route with middleware "auth"
Route::group(['middleware' => 'auth:api'], function() {

    Route::post('/logout', [App\Http\Controllers\Api\Auth\LoginController::class, 'logout']);
});

//group route with prefix "admin"
Route::prefix('admin')->group(function () {
    //group route with middleware "auth:api"
    Route::group(['middleware' => 'auth:api'], function () {
        //dashboard
        Route::get('/dashboard', App\Http\Controllers\Api\Admin\DashboardController::class);

        Route::get('/permissions', [\App\Http\Controllers\Api\Admin\PermissionController::class, 'index'])
        ->middleware('permission:permissions.index');
        //permissions all
        Route::get('/permissions/all', [\App\Http\Controllers\Api\Admin\PermissionController::class, 'all'])
        ->middleware('permission:permissions.index');

        Route::apiResource('/roles', App\Http\Controllers\Api\Admin\RoleController::class)
        ->middleware('permission:roles.index|roles.store|roles.update|roles.delete');

        Route::apiResource('/users', App\Http\Controllers\Api\Admin\UserController::class)
        ->middleware('permission:users.index|users.store|users.update|users.delete');

        //Categories
        Route::apiResource('/categories', App\Http\Controllers\Api\Admin\CategoryController::class)
        ->middleware('permission:categories.index|categories.store|categories.update|categories.delete');

        Route::get('/categories/all', [\App\Http\Controllers\Api\Admin\CategoryController::class, 'all'])
        ->middleware('permission:categories.index');

        Route::apiResource('/posts', App\Http\Controllers\Api\Admin\PostController::class)
        ->middleware('permission:posts.index|posts.store|posts.update|posts.delete');

        Route::apiResource('/products', App\Http\Controllers\Api\Admin\ProductController::class)
        ->middleware('permission:products.index|products.store|products.update|products.delete');

        Route::apiResource('/pages', App\Http\Controllers\Api\Admin\PageController::class)
        ->middleware('permission:pages.index|pages.store|pages.update|pages.delete');

        Route::apiResource('/photos', App\Http\Controllers\Api\Admin\PhotoController::class, ['except' => ['create', 'show', 'update']])
        ->middleware('permission:photos.index|photos.store|photos.delete');

        Route::apiResource('/sliders', App\Http\Controllers\Api\Admin\SliderController::class, ['except' => ['create', 'show', 'update']])
        ->middleware('permission:sliders.index|sliders.store|sliders.delete');

        Route::apiResource('/aparaturs', App\Http\Controllers\Api\Admin\AparaturController::class)
        ->middleware('permission:aparaturs.index|aparaturs.store|aparaturs.update|aparaturs.delete');
    });
});
