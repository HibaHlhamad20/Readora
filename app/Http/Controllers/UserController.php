<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\User;

class UserController extends Controller
{
    public function getPointsHistory(Request $request)
{
    
    $user = $request->user();

    $history = $user->completedBooks()
        ->orderBy('book_user.created_at', 'desc')
        ->get()
        ->map(function ($book) {
            return [
                'type'   => 'Quiz',
                'title'  => $book->book_name,
                'points' => '+' . ($book->pivot->points ?? 0) . ' pts',
                'date'   => $book->pivot->created_at 
                            ? \Carbon\Carbon::parse($book->pivot->created_at)->format('d M Y') 
                            : '',
            ];
        });

    return response()->json([
        'total_points' => $user->points,
        'history'      => $history
    ], 200);
}


    public function getPurchasesHistory(Request $request)
{
    
    $user = $request->user();

    
    $purchases = $user->purchases()->with('book')->get()->map(function ($p) {
        return [
            'title'     => $p->book->book_name ?? 'Unknown Book',
            'type'      => 'Purchase',
            'price'     => $p->price,
            'date'      => $p->created_at ? \Carbon\Carbon::parse($p->created_at)->format('d M Y') : '',
            'timestamp' => $p->created_at,
        ];
    });

    
    $borrowings = $user->borrowings()->with('book')->get()->map(function ($b) {
        return [
            'title'     => $b->book->book_name ?? 'Unknown Book',
            'type'      => 'Rent',
            'price'     => $b->price,
            'date'      => $b->created_at ? \Carbon\Carbon::parse($b->created_at)->format('d M Y') : '',
            'timestamp' => $b->created_at,
        ];
    });

    
    $history = $purchases->concat($borrowings)->sortByDesc('timestamp')->values();

    
    $totalSpent = $history->sum('price');

    
    $formattedHistory = $history->map(function ($item) {
        return [
            'title' => $item['title'],
            'type'  => $item['type'],
            'price' => $item['type'] . ' ' . $item['price'] . ' SYP',
            'date'  => $item['date'],
        ];
    });

    return response()->json([
        'total_spent' => $totalSpent . ' SYP',
        'history'     => $formattedHistory,
    ], 200);
}



public function getMyBooks(Request $request)
{
    
    $user = $request->user();

    $formatRecord = function ($record, $type) {
        $book = $record->book;
        
        
        $authors = $book && $book->authors->isNotEmpty() 
            ? $book->authors->pluck('author_name')->implode(', ') 
            : 'Unknown Author';

        return [
            'book_id'   => $book->id ?? null,
            'book_name' => $book->book_name ?? '',
            'authors'   => $authors,
            'type'      => $type,
            'date'      => $record->created_at ? \Carbon\Carbon::parse($record->created_at)->format('d M Y') : '',
            'timestamp' => $record->created_at,
        ];
    };

    
    $purchasedBooks = $user->purchases()->with('book.authors')->get()
        ->map(fn($p) => $formatRecord($p, 'Purchase'));

    $borrowedBooks = $user->borrowings()->with('book.authors')->get()
        ->map(fn($b) => $formatRecord($b, 'Rent'));

    
    $allBooks = $purchasedBooks->concat($borrowedBooks)
        ->sortByDesc('timestamp')
        ->values()
        ->map(function ($item) {
            unset($item['timestamp']);
            return $item;
        });

    return response()->json([
        'total_books' => $allBooks->count(),
        'books'       => $allBooks
    ], 200);
}



public function getTransactions(Request $request)
{
    $user = $request->user();

    
    $purchases = $user->purchases->map(fn($item) => [
        'type' => 'Purchase',
        'amount' => -$item->price, 
        'date' => $item->created_at
    ]);

    $borrowings = $user->borrowings->map(fn($item) => [
        'type' => 'Borrow',
        'amount' => -$item->price, 
        'date' => $item->created_at
    ]);

    $charges = $user->chargingRequests->where('status', 'approved')->map(fn($item) => [
        'type' => 'Recharge',
        'amount' => $item->amount, 
        'date' => $item->created_at
    ]);

    
    $allTransactions = collect()->merge($purchases)->merge($borrowings)->merge($charges)
                        ->sortByDesc('date') 
                        ->values();

    
    return response()->json([
        'wallet_balance' => $user->wallet, // تأكدي أن اسم العمود بجدول users هو wallet
        'transactions' => $allTransactions
    ]);
}








}
