<?php

// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [App\Http\Controllers\Api\Auth\LoginController::class, 'index']);

//group route with middleware "auth"
Route::group(['middleware' => 'auth:api'], function() {

    //logout
    Route::post('/logout', [App\Http\Controllers\Api\Auth\LoginController::class, 'logout']);
    
});