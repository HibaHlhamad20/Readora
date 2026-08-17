<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Question;
use App\Models\Book;
use App\Models\User;

class QuizController extends Controller
{
      
    public function getQuizQuestions($book_id)
    {
          $user = auth()->user();

    
    $alreadySolved = $user->completedBooks()->where('book_id', $book_id)->exists();
    
    if ($alreadySolved) {
        return response()->json([
            'message' => 'عذراً، لقد قمت باجتياز اختبار هذا الكتاب مسبقاً ولا يمكنك رؤية الأسئلة مجدداً.'
        ], 403); 
    }
        
        $book = Book::findOrFail($book_id);

        
        $questions = Question::where('book_id', $book_id)
            ->select('id', 'book_id', 'question_text', 'options')
            ->get();

    
        if ($questions->isEmpty()) {
            return response()->json([
                'message' => 'لا يوجد اختبار متاح لهذا الكتاب حالياً.'
            ], 404);
        }

        return response()->json([
            'book_name' => $book->book_name,
            'questions' => $questions
        ], 200);
    }

public function submitQuiz(Request $request)
{
    
    $firstAnswer = $request->input('answers.0'); 
    
    if (!$firstAnswer) {
        return response()->json(['message' => 'لم يتم إرسال أي إجابات'], 400);
    }

    
    $question =Question::find($firstAnswer['question_id']);
    
    if (!$question) {
        return response()->json(['message' => 'السؤال غير موجود'], 404);
    }

    
    $bookId = $question->book_id; 
   
     $user = Auth::user();
        
     $alreadySolved=$user->completedBooks()->where('book_id',$bookId)->exists();
     if($alreadySolved){
        return response()->json([
            'message'=>'لقد قمت باجتياز هذا الاختبار سابقا'
        ],403);
     }
        
        $answers = $request->input('answers'); 
        
        if (empty($answers)) {
            return response()->json(['message' => 'لم يتم إرسال أي إجابات لتصحيحها.'], 400);
        }

        $correctCount = 0;
        

        foreach ($answers as $answer) {
            $question = Question::find($answer['question_id']);
            
            if ($question && $question->correct_answer === $answer['user_answer']) {
                $correctCount++; 
            }
        }
        
        
        $pointsToEarn = $correctCount * 1; 
        
        if ($pointsToEarn > 0) {
            $user->increment('points', $pointsToEarn); 
        }
        $user->completedBooks()->attach($bookId,
        ['points'=>$pointsToEarn]);

        return response()->json([
            'correct_answers' => $correctCount,
            'points_earned' => $pointsToEarn,
            'current_total_points' => $user->points, 
            'message' => "لقد أجبت على $correctCount أسئلة بشكل صحيح وحصلت على $pointsToEarn نقاط بونص!"
        ], 200);

}  

  
}