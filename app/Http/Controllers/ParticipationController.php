<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Participation;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ParticipationController extends Controller
{
    public function addParticipation ($event_id) {
        $event = Event::findOrFail($event_id);

        if (Participation::where('user_id',Auth::id())->where('event_id',$event->id)->whereIn('status',['joined','finished'])->exists()) {
            return response()->json(['message'=>'User has joined already'],409);
        }
        
        $participation = Participation::create([
            'user_id'=>Auth::id(),
            'event_id'=>$event->id,
            'status'=>'joined',
            'joined_at'=>now()
        ]);
        $books = $event->books;

        foreach ($books as $book)
        DB::table('participation_books')->insert([
            'participation_id'=>$participation->id,
            'book_id'=>$book->id,
        ]);

        return response()->json(['message'=>'Participation has been created successfully','data'=>$participation],201);
    }

    public function cancelParticipation ($participation_id) {
        $participation = Participation::findOrFail($participation_id);

        if ($participation->status==='cancelled') {
            return response()->json(['message'=>'Participation has already been cancelled'],409);
        }

        $participation->status='cancelled';
        $participation->save();

        return response()->json(['message'=>'Participation has been cancelled successfully','data'=>$participation],200);
    }

    public function showAllParticipations () {
        $event_ids = Participation::where('user_id',Auth::id())->pluck('event_id');
        $events = Event::whereIn('id',$event_ids)->with(['books'])->orderBy('created_at','desc')->get();

        return response()->json($events,200);
    }

    public function showWinParticipations () {
        // $event_ids = Participation::where('user_id',Auth::id())->where('status','finished')->pluck('event_id');
        // $events = Event::whereIn('id',$event_ids)->with(['books'])->orderBy('created_at','desc')->get();

        // return response()->json($events,200);

        
    $participations = Participation::where('user_id', Auth::id())
        ->where('status', 'finished')
        ->with(['event' => function($query) {
            $query->with('books');
        }])
        ->orderBy('updated_at', 'desc')
        ->get();

    
    $events = $participations->map(function ($participation) {
        return [
            'participation_id' => $participation->id,
            'event_id'         => $participation->event->id ?? null,
            'event_name'       => $participation->event->event_name ?? '',
            'points'           => $participation->event->points ?? 0,
            'date'             => $participation->updated_at->format('Y-m-d'), 
            'books'            => $participation->event->books ?? [],
        ];
    });

    return $events;
    }

    public function showWinQuizes(Request $request){
        
    $user = $request->user();

    $quizzes = $user->completedBooks()
        ->orderBy('book_user.created_at', 'desc')
        ->get()
        ->map(function ($book) {
            return [
                'type'   => 'Quiz',
                'title'  => $book->book_name,
                'points' => $book->pivot->points ?? 0 ,
                'date'   => $book->pivot->created_at 
                            ? \Carbon\Carbon::parse($book->pivot->created_at)->format('d M Y') 
                            : '',
            ];
        });

    return $quizzes;
    }

    public function getMyWinsOverview()
    {
    
    $events = $this->showWinParticipations();

    
    $quizzes = $this->showWinQuizes();

    
    $totalWins = count($events) + count($quizzes);

    return response()->json([
        'status'     => true,
        'total_wins' => $totalWins,
        'wins'       => $events->concat($quizzes), 
        'events'     => $events,
        'quizzes'    => $quizzes,
    ], 200);
    }

    public function showLoseParticipations () {
        $event_ids = Participation::where('user_id',Auth::id())->where('status','joined')->pluck('event_id');
        $events = Event::whereIn('id',$event_ids)->with(['books'])->orderBy('created_at','desc')->where('status','completed')->get();

        return response()->json($events,200);
    }
}
