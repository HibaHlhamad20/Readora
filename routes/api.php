<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth:sanctum'])->group(function(){
//1
    Route::get('/user', function (Request $request) {
    return $request->user();
});

//2
    Route::post('/profile_update',[RegisteredUserController::class,'updateProfile']);
//3
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);
});
//without middleware
//1
Route::post('/register', [RegisteredUserController::class, 'store']);
//2
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
//3
Route::post('/password/forgot', [PasswordResetController::class, 'sendOtp']);
//4
Route::post('/password/reset', [PasswordResetController::class, 'resetPassword']);
//5
Route::get('/categories',[CategoryController::class,'index']);


