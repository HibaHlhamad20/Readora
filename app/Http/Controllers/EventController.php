<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Event;
use App\Models\Participation;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    public function addEvent (StoreEventRequest $request) {
        $validated = $request->validated();
        $event = Event::create(Arr::except($validated, ['book_ids']));
        $event->books()->sync($validated['book_ids']);

        return response()->json(['message'=>'Event added successfully','data'=>$event->load('books')],201);
    }

    public function updateEvent (UpdateEventRequest $request, $id) {
        $event = Event::findOrFail($id);
        $validated = $request->validated();
        $event->update(Arr::except($validated, ['book_ids']));

        if ($request->has('book_ids')) {
            $event->books()->sync($validated['book_ids']);
        }

        return response()->json(['message'=>'Event updated successfully','data'=>$event->load('books')],200);
    }

    public function cancelEvent ($id) {
        $event = Event::findOrFail($id);
        $event->status = 'cancelled';
        $event->save();

        return response()->json(['message'=>'Event cancelled successfully','data'=>$event->load('books')],200);
    }

    public function showEvents () {
        $events = Event::with(['books'])->orderBy('created_at','desc')->get();

        return response()->json($events,200);
    }

    public function showUpcomingEvents () {
        $events = Event::where('status','upcoming')->with(['books'])->orderBy('created_at','desc')->get();

        return response()->json($events,200);
    }

    public function showOngoingEvents () {
        $events = Event::where('status','ongoing')->with(['books'])->orderBy('created_at','desc')->get();

        return response()->json($events,200);
    }

    public function showCompletedEvents () {
        $events = Event::where('status','completed')->with(['books'])->orderBy('created_at','desc')->get();

        return response()->json($events,200);
    }

    public function showCancelledEvents () {
        $events = Event::where('status','cancelled')->with(['books'])->orderBy('created_at','desc')->get();

        return response()->json($events,200);
    }

    public function showUpcomingEvent ($id) {
        $event = Event::where('id',$id)->where('status','upcoming')->firstOrFail();

        return response()->json($event->load(['books']),200);
    }

    public function showOngoingEvent ($id) {   
        $event = Event::where('id',$id)->where('status','ongoing')->firstOrFail();
        $participation = Participation::where('event_id',$id)->where('user_id',Auth::id())->first();
        if ($participation)
            {
                $participationBooks = DB::table('participation_books')->
                join('books','books.id','=','participation_books.book_id')->
                where('participation_id',$participation->id)->
                select('books.*','participation_books.status','participation_books.finished_at')->
                get();
                return response()->json(([
                'event'=>$event,
                'books'=>$participationBooks
            ]),200);
            }
        else {
            return response()->json(([
                'event'=>$event,
                'books'=>$event->books
            ]),200);
        }  
    }

    public function showCompletedEvent ($id) {   
        $event = Event::where('id',$id)->where('status','completed')->firstOrFail();
        $winners = User::join('participations','participations.user_id','=','users.id')->join('events','events.id','=','participations.event_id')->where('event_id',$id)->where('participations.status','finished')
        ->select('users.name','events.points')->get();
        $participation = Participation::where('event_id',$id)->where('user_id',Auth::id())->first();
        if ($participation)
            {
                $participationBooks = DB::table('participation_books')->
                join('books','books.id','=','participation_books.book_id')->
                where('participation_id',$participation->id)->
                select('books.*','participation_books.status','participation_books.finished_at')->
                get();
                return response()->json(([
                'event'=>$event,
                'books'=>$participationBooks,
                'winners'=>$winners
            ]),200);
            }
        else {
            return response()->json(([
                'event'=>$event,
                'books'=>$event->books,
                'winners'=>$winners
            ]),200);
        }  
    }

    public function showCancelledEvent ($id) {
        $event = Event::where('id',$id)->where('status','cancelled')->firstOrFail();

        return response()->json($event->load(['books']),200);
    }

    public function showParticipatedEvent ($id) {   
        $event = Event::findOrFail($id);
        if ($event->status=='completed')
            {
        $winners = User::join('participations','participations.user_id','=','users.id')->join('events','events.id','=','participations.event_id')->where('event_id',$id)->where('participations.status','finished')
        ->select('users.name','events.points')->get();
        $participation = Participation::where('event_id',$id)->where('user_id',Auth::id())->first();
        if ($participation)
            {
                $participationBooks = DB::table('participation_books')->
                join('books','books.id','=','participation_books.book_id')->
                where('participation_id',$participation->id)->
                select('books.*','participation_books.status','participation_books.finished_at')->
                get();
                return response()->json(([
                'event'=>$event,
                'books'=>$participationBooks,
                'winners'=>$winners
            ]),200);
            }
            }
        if ($event->status=='ongoing')
            {
        $participation = Participation::where('event_id',$id)->where('user_id',Auth::id())->first();
        if ($participation)
            {
                $participationBooks = DB::table('participation_books')->
                join('books','books.id','=','participation_books.book_id')->
                where('participation_id',$participation->id)->
                select('books.*','participation_books.status','participation_books.finished_at')->
                get();
                return response()->json(([
                'event'=>$event,
                'books'=>$participationBooks
            ]),200);
            }
            }
    }

    // public function showEvent ($id) {
    //     $event = Event::findOrFail($id);
    //     return response()->json($event->load(['books','participations']),200);
    // }


 
}
