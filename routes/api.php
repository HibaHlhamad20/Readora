<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
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

//عرض 30 اقتراح كتاب حسب اهتمامات المستخدم
Route::get('/books/recommended',[BookController::class,'showBooksByUserInterests']);
//لشحن المحفظة  هاد يلي لازم تربطيه نور
Route::post('/wallet_charge',[WalletController::class,'sendChargingRequest']);


//راوتات الادمن 
 Route::middleware('admin')->group(function()
 {
    Route::get('/pending_request',[WalletController::class,'getPendingRequet']);
    Route::post('/approve_request/{id}',[WalletController::class,'approveRequest']);
    Route::post('/reject_request/{id}',[WalletController::class,'rejectRequest']);

 });
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


//إضافة مؤلف
Route::post('/authors',[AuthorController::class,'addAuthor']);
//تعديل بيانات مؤلف
Route::put('/authors/{id}',[AuthorController::class,'updateAuthor']);
//حذف مؤلف
Route::delete('/authors/{id}',[AuthorController::class,'deleteAuthor']);
//عرض جميع المؤلفين
Route::get('/authors',[AuthorController::class,'showAuthors']);

//إضافة كتاب
Route::post('/books',[BookController::class,'addBook']);
//تعديل بيانات كتاب
Route::put('/books/{id}',[BookController::class,'updateBook']);
//حذف كتاب
Route::delete('/books/{id}',[BookController::class,'deleteBook']);
//عرض جميع الكتب مرتبة من الأحدث للأقدم
Route::get('/books',[BookController::class,'showBooks']);

//عرض أعلى 30 كتاب تقييماً
Route::get('/books/top-rated',[BookController::class,'showBooksByRating']);
//عرض آخر 30 كتاب مضاف
Route::get('/books/new',[BookController::class,'showNewBooks']);
//عرض 30 اقتراح كتاب حسب اهتمامات المستخدم
Route::get('/books/recommended',[BookController::class,'showBooksByUserInterests']);
//عرض تفاصيل الكتاب 
Route::get('/books/details/{id}',[BookController::class,'showBookDetails']);






//5|GugMoPhG2n32DHQVdbH96KT2EBaByXe9IF93sUXx94ee45b6
//6|ZYhfXldxJ6bzfo7Tw0FfokCGZdpkmaZ6wVrWArZl0f070c15