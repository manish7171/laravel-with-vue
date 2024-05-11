<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserListController;
use App\Http\Controllers\API\UserCreateController;
use App\Http\Controllers\API\UserUpdateController;
use App\Http\Controllers\API\UserDeleteController;
use App\Http\Controllers\API\UserShowController;

Route::get('/users', UserListController::class);
Route::get('/user/{userId}', UserShowController::class);
Route::post('/user', UserCreateController::class);
Route::put('/user/{userId}', UserUpdateController::class);
Route::delete('/user/{userId}', UserDeleteController::class);
// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
