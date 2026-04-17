<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PasswordResetController;
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
});
//without middleware
//1
Route::post('/password/forgot', [PasswordResetController::class, 'sendOtp']);
//2
Route::post('/password/reset', [PasswordResetController::class, 'resetPassword']);
//3
Route::get('/categories',[CategoryController::class,'index']);


