<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Purchase;
use App\Models\Borrowing;
use App\Models\Event;
use App\Models\Participation;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\DB;

class BookActionController extends Controller
{
    
    public function purchase(Request $request)
    {
        
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'discount_package' => 'required|in:none,15,30,45,60,75', 
        ]);

        $user = Auth::user();
        $book = Book::findOrFail($request->book_id);

        
        $alreadyPurchased = Purchase::where('user_id', $user->id)->where('book_id', $book->id)->exists();
        if ($alreadyPurchased) {
            return response()->json(['message' => 'لقد قمت بشراء هذا الكتاب سابقاً وهو متاح لك دائماً'], 400);
        }

        $basePrice = $book->selling_price; 
        $discountPercentage = 0;   
        $pointsToDeduct = 0;       

        
        if ($request->discount_package !== 'none') {
            $package = (int)$request->discount_package;

            
            if ($user->points < $package) {
                return response()->json(['message' => 'رصيد نقاطك غير كافٍ لتفعيل هذا الخصم'], 400);
            }

            
            if ($package === 15) { $discountPercentage = 10; $pointsToDeduct = 15; }
            elseif ($package === 30) { $discountPercentage = 20; $pointsToDeduct = 30; }
            elseif ($package === 45) { $discountPercentage = 30; $pointsToDeduct = 45; }
            elseif ($package === 60) { $discountPercentage = 40; $pointsToDeduct = 60; }
            elseif ($package === 75) { $discountPercentage = 50; $pointsToDeduct = 75; }
        }

        
        $discountAmount = ($basePrice * $discountPercentage) / 100;
        $finalPrice = $basePrice - $discountAmount;

        
        if ($user->wallet < $finalPrice) {
            return response()->json(['message' => 'رصيد محفظتك المالي غير كافٍ لإتمام عملية الشراء'], 400);
        }

        
        if ($pointsToDeduct > 0) {
            $user->decrement('points', $pointsToDeduct);
        }
        $user->decrement('wallet', $finalPrice);

        
        Purchase::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'price' => $finalPrice,
            'discount_type' => $request->discount_package === 'none' ? 'none' : 'points',
        ]);


        
    
    if (!empty($user->fcm_token)) {
        FirebaseService::sentNotification(
            $user->fcm_token,
            'مبارك إضافتك الجديدة! 📚🎉',
            "تم شراء كتاب \"{$book->title}\" بنجاح، وأصبح متاحاً في مكتبتك الخاصة مدى الحياة!"
        );
    }

    

        return response()->json([
            'message' => 'تم شراء الكتاب بنجاح وتم فتحه لك مدى الحياة!',
            'final_price' => $finalPrice,
            'remaining_wallet' => $user->wallet,
            'remaining_points' => $user->points,
        ], 200);
    }


    



    
    public function borrow(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'discount_package' => 'required|in:none,15,30,45,60,75',
        ]);

        $user = Auth::user();
        $book = Book::findOrFail($request->book_id);

    
        $hasActiveBorrow = Borrowing::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->where('status', 'active')
            ->where('expires_at', '>', Carbon::now())
            ->exists();

        if ($hasActiveBorrow) {
            return response()->json(['message' => 'هذا الكتاب مستعار لديك حالياً وفترته لم تنتهِ بعد'], 400);
        }
        
        $baseBorrowPrice = $book->rental_price;
        $discountPercentage = 0;
        $pointsToDeduct = 0;

        
        if ($request->discount_package !== 'none') {
            $package = (int)$request->discount_package;

            if ($user->points < $package) {
                return response()->json(['message' => 'رصيد نقاطك غير كافٍ لتفعيل هذا الخصم'], 400);
            }

            if ($package === 15) { $discountPercentage = 10; $pointsToDeduct = 15; }
            elseif ($package === 30) { $discountPercentage = 20; $pointsToDeduct = 30; }
            elseif ($package === 45) { $discountPercentage = 30; $pointsToDeduct = 45; }
            elseif ($package === 60) { $discountPercentage = 40; $pointsToDeduct = 60; }
            elseif ($package === 75) { $discountPercentage = 50; $pointsToDeduct = 75; }
        }

        
        $discountAmount = ($baseBorrowPrice * $discountPercentage) / 100;
        $finalBorrowPrice = $baseBorrowPrice - $discountAmount;

        
        if ($user->wallet < $finalBorrowPrice) {
            return response()->json(['message' => 'رصيد محفظتك المالي غير كافٍ للاستعارة'], 400);
        }

        
        if ($pointsToDeduct > 0) {
            $user->decrement('points', $pointsToDeduct);
        }
        $user->decrement('wallet', $finalBorrowPrice);

        
        $borrowedAt = Carbon::now();
        $expiresAt = Carbon::now()->addDays(30);

        
        Borrowing::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'price' => $finalBorrowPrice,
            'discount_type' => $request->discount_package === 'none' ? 'none' : 'points',
            'borrowed_at' => $borrowedAt,
            'expires_at' => $expiresAt,
            'status' => 'active'
        ]);

        
        if (!empty($user->fcm_token)) {
            FirebaseService::sentNotification(
                $user->fcm_token,
                'استعارة مباركة! 📖',
                "تمت استعارة كتاب {$book->title} بنجاح! فترة الاستعارة متاحة حتى " . $expiresAt->format('Y-m-d')
            );
        }


        return response()->json([
            'message' => 'تم استعارة الكتاب بنجاح لمدة شهر واحد!',
            'final_price' => $finalBorrowPrice,
            'expires_at' => $expiresAt->toDateTimeString(),
            'remaining_wallet' => $user->wallet,
            'remaining_points' => $user->points,
        ], 200);
    }




    public function checkAccess($book_id){
        $user=Auth::user();
        $is_purchase=Purchase::where('user_id',$user->id)->where('book_id',$book_id)->exists();
        if($is_purchase){
            return response()->json([
                'has_access'=>true,
                'message'=>'الكتاب متاح للقراءة دائما'
            ],200);
        }
        $active_borrow=Borrowing::where('user_id',$user->id)->where('book_id',$book_id)
        ->where('status','active')->where('expires_at', '>', Carbon::now())->exists();

        if($active_borrow){
            return response()->json([
                'has_access'=>true,
                'message'=>'الكتاب متاح لك للقراءة'
            ],200);
        }
        $events_id = DB::table('event_book')->where('book_id',$book_id)->pluck('event_id');
        $event_ids = Event::whereIn('id',$events_id)->where('status','ongoing')->pluck('id');
        $participation_ids = Participation::where('user_id',Auth::id())->where('status','joined')->whereIn('event_id',$event_ids)->pluck('id');
        if ($participation_ids->isNotEmpty()) {
            return response()->json([
                'has_access'=>true,
                'message'=>'Book is available as part of the event'
            ],200);
        }
        return response()->json([
            'has_access'=>false,
            'message'=>'عذرا هذا الكتاب مغلق ! يجب عليك شراؤه او استعارته'
        ],200);

    }
}