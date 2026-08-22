<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Book;
use App\Models\Purchase;
use App\Models\Borrowing;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules;

class AdminDashboardController extends Controller
{
    public function getStatistics()
    {
        // 1
        $totalUsers = User::count(); 
        //2
        $totalBooks = Book::count(); 

        // 3
        $purchaseRevenue = Purchase::sum('price'); 
        $borrowingRevenue = Borrowing::sum('price');
        $totalRevenue = $purchaseRevenue + $borrowingRevenue; 

        //4
        $activeBorrowings = Borrowing::where('status', 'active')
            ->where('expires_at', '>', Carbon::now())
            ->count();
//.........................................................

        // اكثر 3 كتب قراءة 
        $topBooks = Book::withCount(['purchases', 'borrowings'])
            ->get()
            ->map(function ($book) {
                
                $totalReaders = $book->purchases_count + $book->borrowings_count;

            
                $bookPurchaseRevenue = Purchase::where('book_id', $book->id)->sum('price');
                $bookBorrowingRevenue = Borrowing::where('book_id', $book->id)->sum('price');
                $totalBookRevenue = $bookPurchaseRevenue + $bookBorrowingRevenue;

                return [
                    'id' => $book->id,
                    'book_name' => $book->book_name,
                    'cover_image' => asset('storage/' . $book->cover_image), 
                    'total_readers' => $totalReaders,
                    'total_revenue' => $totalBookRevenue
                ];
            })
            ->sortByDesc('total_readers') 
            ->take(3) 
            ->values(); 
//.......................................................

        // الجدول البياني
        $chartData = [];
        for ($i = 11; $i >= 0; $i--) {
            
            $month = Carbon::now()->subMonths($i);
            $monthName = $month->translatedFormat('F'); // اسم الشهر (يناير، فبراير، مارس...)

            
            $monthPurchaseSum = Purchase::whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->sum('price');

            $monthBorrowingSum = Borrowing::whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->sum('price');

            $chartData[] = [
                'month' => $monthName,
                'revenue' => $monthPurchaseSum + $monthBorrowingSum
            ];
        }



        return response()->json([
            'cards' => [
                'total_users' => $totalUsers,
                'total_books' => $totalBooks,
                'total_revenue' => $totalRevenue,
                'active_borrowings' => $activeBorrowings,
            ],
            'top_books' => $topBooks,
            'chart_data' => $chartData
        ], 200);
    }

//......................................................




public function getAllUsers(){


    $users = User::select('id', 'name', 'email','created_at')
                 ->latest()
                 ->get();

    return response()->json([
        'users' => $users
    ], 200);
}


public function getUserDetails($id)
{

    $user = User::findOrFail($id);

    
    $purchasesCount = $user->purchases()->count();
    $borrowingsCount = $user->borrowings()->count();

    return response()->json([
        'user' => $user,
        'statistics' => [
            'purchases_count' => $purchasesCount,
            'borrowings_count' => $borrowingsCount
        ]
    ], 200);
}

//................................................



public function addAdmin(Request $request)
{
    
    $request->validate([
        'name'     => 'required|string|max:255',
        'email'    => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8',
    ]);

    
    $admin = User::create([
        'name'     => $request->name,
        'email'    => $request->email,
        'password' => Hash::make($request->password),
        'role'     => 'admin', 
    ]);

    return response()->json([
        'status'  => true,
        'message' => 'تم إضافة المسؤول (Admin) بنجاح',
        'data'    => $admin
    ], 201);
}

}