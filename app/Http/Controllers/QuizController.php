<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Question;
use App\Models\Book;
use App\Models\Event;
use App\Models\Participation;
use App\Models\User;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
      
    public function getQuizQuestions($book_id)
    {
          $user = auth()->user();

    
    $alreadySolved = $user->completedBooks()->where('book_id', $book_id)->exists();
    
    if ($alreadySolved) {
        return response()->json([
            'message' => 'عذراً، لقد قمت بأداء اختبار هذا الكتاب من قبل ولا يمكنك إعادته.'
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

// public function submitQuiz(Request $request)
// {
    
//     $firstAnswer = $request->input('answers.0'); 
    
//     if (!$firstAnswer) {
//         return response()->json(['message' => 'لم يتم إرسال أي إجابات'], 400);
//     }

    
//     $question =Question::find($firstAnswer['question_id']);
    
//     if (!$question) {
//         return response()->json(['message' => 'السؤال غير موجود'], 404);
//     }

    
//     $bookId = $question->book_id; 
   
//      $user = Auth::user();
        
//      $alreadySolved=$user->completedBooks()->where('book_id',$bookId)->exists();
//      if($alreadySolved){
//         return response()->json([
//             'message'=>'لقد قمت باجتياز هذا الاختبار سابقا'
//         ],403);
//      }
        
//         $answers = $request->input('answers'); 
        
//         if (empty($answers)) {
//             return response()->json(['message' => 'لم يتم إرسال أي إجابات لتصحيحها.'], 400);
//         }

//         $correctCount = 0;
        

//         foreach ($answers as $answer) {
//             $question = Question::find($answer['question_id']);
            
//             if ($question && $question->correct_answer === $answer['user_answer']) {
//                 $correctCount++; 
//             }
//         }
        
        
//         $pointsToEarn = $correctCount * 1; 
        
//         if ($pointsToEarn > 0) {
//             $user->increment('points', $pointsToEarn); 
//         }
//         $user->completedBooks()->attach($bookId,
//         ['points'=>$pointsToEarn]);

//         return response()->json([
//             'correct_answers' => $correctCount,
//             'points_earned' => $pointsToEarn,
//             'current_total_points' => $user->points, 
//             'message' => "لقد أجبت على $correctCount أسئلة بشكل صحيح وحصلت على $pointsToEarn نقاط بونص!"
//         ], 200);

// }  

public function submitQuiz(Request $request)
{
    $answers = $request->input('answers'); 
    
    if (empty($answers)) {
        return response()->json(['message' => 'لم يتم إرسال أي إجابات'], 400);
    }

    $firstAnswer = $answers[0]; 
    $question = Question::find($firstAnswer['question_id']);
    
    if (!$question) {
        return response()->json(['message' => 'السؤال غير موجود'], 404);
    }

    $bookId = $question->book_id; 
    $user = Auth::user();
        
    
    $alreadySolved = $user->completedBooks()->where('book_id', $bookId)->exists();
    if ($alreadySolved) {
        return response()->json([
            'message' => 'لقد قمت بأداء اختبار هذا الكتاب من قبل ولا يمكنك إعادته'
        ], 403);
    }

    $totalQuestions = Question::where('book_id', $bookId)->count();
    $correctCount = 0;

    
    foreach ($answers as $answer) {
        $q = Question::find($answer['question_id']);
        
        if ($q && $q->correct_answer === $answer['user_answer']) {
            $correctCount++; 
        }
    }
    
    
    $pointsToEarn = 0;
    $passed = false;

    if ($correctCount === $totalQuestions && $totalQuestions > 0 && count($answers) === $totalQuestions) {
        $pointsToEarn = 3; // إعطاء 3 نقاط فقط في حال الإجابة الكاملة
        $passed = true;
        $user->increment('points', $pointsToEarn); 
    }


    $user->completedBooks()->attach($bookId, [
        'points' => $pointsToEarn
    ]);

    
    if ($passed) {
           if (!empty($user->fcm_token)) {
            FirebaseService::sentNotification(
                $user->fcm_token,
                'تهانينا! 🎉',
                "لقد أجبت على جميع الأسئلة بشكل صحيح وحصلت على {$pointsToEarn} نقاط جديدة!"
            );
        }

        $events_id = DB::table('event_book')->where('book_id',$bookId)->pluck('event_id');
        $event_ids = Event::whereIn('id',$events_id)->where('status','ongoing')->pluck('id');
        $participation_ids = Participation::where('user_id',Auth::id())->where('status','joined')->whereIn('event_id',$event_ids)->pluck('id');
        if ($participation_ids->isNotEmpty()) {
            DB::table('participation_books')->whereIn('participation_id',$participation_ids)->where('book_id',$bookId)->update([
                'status'=>'finished',
                'finished_at'=>now()
            ]);
            $participation_ids = DB::table('participation_books')->whereIn('participation_id',$participation_ids)->where('book_id',$bookId)->pluck('participation_id');
            foreach ($participation_ids as $participation_id)
                {
            $not_finished_books = DB::table('participation_books')->where('participation_id',$participation_id)->where('status','not_finished')->exists();
            if (!$not_finished_books)
                {
                    $participation = Participation::where('id',$participation_id)->first();
                    $participation->status = 'finished';
                    $participation->finished_at = now();
                    $participation->save();
                }
                }
        }
        return response()->json([
            'passed' => true,
            'correct_answers' => $correctCount,
            'points_earned' => $pointsToEarn,
            'current_total_points' => $user->fresh()->points, 
            'message' => "تهانينا! لقد أجبت على جميع الأسئلة بشكل صحيح وحصلت على $pointsToEarn نقاط!"
        ], 200);
    }

    return response()->json([
        'passed' => false,
        'correct_answers' => $correctCount,
        'points_earned' => 0,
        'current_total_points' => $user->points, 
        'message' => "للأسف، لم تتجاوز الاختبار. يجب الإجابة على جميع الأسئلة بشكل صحيح للحصول على النقاط."
    ], 200);
}

  
}