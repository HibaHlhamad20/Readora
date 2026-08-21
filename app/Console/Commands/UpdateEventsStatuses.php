<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\Participation;
use App\Models\User;
use Illuminate\Console\Command;

class UpdateEventsStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-events-statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $updated = Event::where('status','upcoming')->where('start_date',today())->update([
            'status'=>'ongoing'
        ]);
        if ($updated) {
            //إشعار للمشاركين ببدء الفعالية
        }


        $events_ids = Event::where('status','ongoing')->where('end_date',today())->pluck('id');
        $updated = Event::where('status','ongoing')->where('end_date',today())->update([
            'status'=>'completed'
        ]);
        if ($updated) {
            //إشعار للمشاركين بانتهاء الفعالية
            $events = Event::whereIn('id',$events_ids)->get();
            foreach ($events as $event) {
                $winners_ids = Participation::where('event_id',$event->id)->where('status','finished')->pluck('user_id');
                $winners = User::whereIn('id',$winners_ids)->get();
                foreach($winners as $winner){
                    $user = User::where('id',$winner->id)->first();
                    $user->points = $user->points + $event->points;
                    $user->save();
                }
            }
        }

    }
}
