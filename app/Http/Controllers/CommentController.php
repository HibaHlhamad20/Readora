<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function addComment (Request $request, $bookId) {
        $book=Book::findOrFail($bookId);
        $validated = $request-> validate([
            'body' => 'required|string|min:10|max:5000'
        ]);

        $comment = Comment::create([
            'user_id'=> Auth::id(),
            'book_id'=> $book->id,
            'body' => $validated['body']
        ]);

        return response()->json(['message'=>'Comment has been added successfully','data'=>$comment], 201);
    }

    public function deleteComment ($id) {
        $comment=Comment::findOrFail($id);
        if (Auth::id() !== $comment->user_id)
            {
                return response()->json(['message'=>'Comment must be deleted by the same user','data'=>null], 403);
            }
        $comment->delete();
        return response()->json(['message'=>'Comment has been deleted successfully','data'=>null], 200);
    }

    public function showComments ($bookId) {
        $book=Book::findOrFail($bookId); 
        $comments=$book->comments()->with('users')->orderBy('created_at','desc')->get();

        return response()->json($comments,200);

    }
}
