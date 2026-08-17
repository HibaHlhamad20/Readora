<?php

use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\Api\AdminDashboardController;
use App\Http\Controllers\Api\BookActionController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CommentController;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function(){

    //التعليقات
    //إضافة تعليق
    Route::post('comments/{bookId}',[CommentController::class,'addComment']);
    //حذف تعليق
    Route::delete('/comments/{commentId}',[CommentController::class,'deleteComment']);
    //عرض جميع التعليقات على كتاب معين
    Route::get('/comments/{bookId}',[CommentController::class,'showComments']);

    //المفضلة
    //إضافة كتاب للمفضلة
    Route::post('/books/{id}/favourite',[BookController::class,'addToFavourite']);
    //حذف كتاب من المفضلة
    Route::delete('/books/{id}/favourite',[BookController::class,'removeFromFavourite']);
    //عرض جميع الكتب في المفضلة
    Route::get('/books/favourite',[BookController::class,'showFavourites']);

    //إضافة تقييم لكتاب
    Route::post('/ratings/{id}',[BookController::class,'addRating']);
    //تعديل تقييم كتاب
    Route::put('/ratings/{id}',[BookController::class,'updateRating']);

//1
    Route::get('/user', function (Request $request) {
    return $request->user()->load('categories');
});

//2
    Route::post('/profile_update',[RegisteredUserController::class,'updateProfile']);
//3
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);

//لشحن المحفظة  هاد يلي لازم تربطيه نور
Route::post('/wallet_charge',[WalletController::class,'sendChargingRequest']);

//الشراء
Route::post('/user/book/purchase',[BookActionController::class,'purchase']);
//الاستعارة
Route::post('/user/book/borrow',[BookActionController::class,'borrow']);
//has_access
Route::get('/user/book/{book_id}/check_access',[BookActionController::class,'checkAccess']);


//جلب ال3 اسئلة تبع كتاب محدد
Route::get('/user/books/{book_id}/quiz',[QuizController::class,'getQuizQuestions']);
//لتصحيح الاسئلة وحساب النقاط
Route::post('/user/quiz/submit',[QuizController::class,'submitQuiz']);
//getPointsHistory
Route::get('/user/points_history',[UserController::class,'getPointsHistory']);
//my books
Route::get('/user/my-books', [UserController::class, 'getMyBooks']);
//get purchase history
Route::get('/user/purchases-history', [UserController::class, 'getPurchasesHistory']);



//راوتات الادمن 
Route::middleware('admin')->group(function()
 {
    Route::get('/pending_request',[WalletController::class,'getPendingRequet']);
    Route::post('/approve_request/{id}',[WalletController::class,'approveRequest']);
    Route::post('/reject_request/{id}',[WalletController::class,'rejectRequest']);

    //إضافة مؤلف
    Route::post('/authors',[AuthorController::class,'addAuthor']);
    //تعديل بيانات مؤلف
    Route::put('/authors/{id}',[AuthorController::class,'updateAuthor']);
    //حذف مؤلف
    Route::delete('/authors/{id}',[AuthorController::class,'deleteAuthor']);

    //إضافة كتاب
    Route::post('/books',[BookController::class,'addBook']);
    //تعديل بيانات كتاب
    Route::put('/books/{id}',[BookController::class,'updateBook']);
    //حذف كتاب
    Route::delete('/books/{id}',[BookController::class,'deleteBook']);
    //الاحصائيات باول واجهة
    Route::get('/admin/dashboard/statistics',[AdminDashboardController::class,'getStatistics']);
    //ادارة المستخدمين
    Route::get('/admin/users', [AdminDashboardController::class, 'getAllUsers']); 
    Route::get('/admin/users/{id}', [AdminDashboardController::class, 'getUserDetails']); 

 });
});  

//   Route::middleware('user')->group(function()
//  {
//     //عرض 30 اقتراح كتاب حسب اهتمامات المستخدم
//     Route::get('/books/recommended',[BookController::class,'showBooksByUserInterests']);
//     //المفضلة
//     //إضافة كتاب للمفضلة
//     Route::post('/books/{id}/favourite',[BookController::class,'addToFavourite']);
//     //حذف كتاب من المفضلة
//     Route::delete('/books/{id}/favourite',[BookController::class,'removeFromFavourite']);
//     //عرض جميع الكتب في المفضلة
//     Route::get('/books/favourite',[BookController::class,'showFavourites']);

//  });

//without middleware
//راوت تسجيل دخول الادمن جودي
Route::post('/admin_login',[AdminAuthController::class,'login']);
//1
Route::post('/register', [RegisteredUserController::class, 'store']);
//2
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
//3
Route::post('/password/forgot', [PasswordResetController::class, 'sendOtp']);
//4
Route::post('/password/reset', [PasswordResetController::class, 'resetPassword']);
//5جودي
Route::get('/categories',[CategoryController::class,'index']);

//عرض جميع المؤلفين
Route::get('/authors',[AuthorController::class,'showAuthors']);
//عرض جميع الكتب مرتبة من الأحدث للأقدم
Route::get('/books',[BookController::class,'showBooks']);
//عرض أعلى 30 كتاب تقييماً
Route::get('/books/top-rated',[BookController::class,'showBooksByRating']);
//عرض آخر 30 كتاب مضاف
Route::get('/books/new',[BookController::class,'showNewBooks']);
//عرض 30 اقتراح كتاب حسب اهتمامات المستخدم
// Route::get('/books/recommended',[BookController::class,'showBooksByUserInterests']);
//عرض تفاصيل الكتاب 
Route::get('/books/details/{id}',[BookController::class,'showBookDetails']);
//عرض الكتب مع فلترة للبحث
Route::get('/books/search',[BookController::class,'searchBooks']);
//بحث عن المؤلف حسب الاسم
Route::get('/authors/search',[BookController::class,'searchAuthors']);

//  Route::middleware('user')->group(function()
//  {
//عدلت هون
// Route::middleware(['auth:sanctum'])->group(function()
// {
// //المفضلة
// //إضافة كتاب للمفضلة
// Route::post('/books/{id}/favourite',[BookController::class,'addToFavourite']);
// //حذف كتاب من المفضلة
// Route::delete('/books/{id}/favourite',[BookController::class,'removeFromFavourite']);
// //عرض جميع الكتب في المفضلة
// Route::get('/books/favourite',[BookController::class,'showFavourites']);

//  });

//1|h1nlWxHbF2slIOzkfMq0pjkl7Vy6yRqS4LGMCbJe93bb67bb//user
//2|3ZPxmUWwSA6FuJvbPi80SF2YB3iLyS8Ym6fS5HQIc29a55a3
//3|L8m7F7RPRvb1SOf32p7aYNseGAQTwHn1ctxNqVtd563e5a25//user