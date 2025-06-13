<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/auth/google', [App\Http\Controllers\AuthController::class, 'login']);

Route::get('/auth/google/callback', [App\Http\Controllers\AuthController::class, 'handleGoogleCallback']);

Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])
    ->middleware('auth:sanctum');

Route::get('/user', [App\Http\Controllers\AuthController::class, 'getUser'])
    ->middleware('auth:sanctum');


Route::get('/allUsers',[App\Http\Controllers\AuthController::class, 'getAllUsers'])
    ->middleware('auth:sanctum');
