<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Participation;
use Illuminate\Support\Facades\Auth;

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
}
