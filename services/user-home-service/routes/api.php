<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\HomeController;

/*
|--------------------------------------------------------------------------
| User APIs
|--------------------------------------------------------------------------
*/
Route::get('/all-users', [UserController::class, 'index']);
Route::post('/users', [UserController::class, 'store']);
Route::get('/users/{id}', [UserController::class, 'show']);
Route::put('/users/{id}', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);
Route::get('/users/{userId}/home-details', [HomeController::class, 'getUserHome']);
/*
|--------------------------------------------------------------------------
| Home APIs
|--------------------------------------------------------------------------
*/
Route::get('/homes', [HomeController::class, 'index']);
Route::post('/homes', [HomeController::class, 'store']);
Route::get('/homes/{id}', [HomeController::class, 'show']);
Route::put('/homes/{id}', [HomeController::class, 'update']);
Route::delete('/homes/{id}', [HomeController::class, 'destroy']);
Route::post('/homes/{homeId}/members', [HomeController::class, 'addMember']);
