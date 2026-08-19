<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Event;
use Illuminate\Support\Arr;

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

    public function showEvent ($id) {
        $event = Event::findOrFail($id);

        return response()->json($event,200);
    }
 
}
