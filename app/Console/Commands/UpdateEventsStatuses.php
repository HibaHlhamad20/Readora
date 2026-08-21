<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\Participation;
use App\Models\User;
use Illuminate\Console\Command;
use App\Services\FirebaseService;

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
    

        // $updated = Event::where('status','upcoming')->where('start_date',today())->update([
        //     'status'=>'ongoing'
        // ]);
        // if ($updated) {
        //     //إشعار للمشاركين ببدء الفعالية
        // }


        // $events_ids = Event::where('status','ongoing')->where('end_date',today())->pluck('id');
        // $updated = Event::where('status','ongoing')->where('end_date',today())->update([
        //     'status'=>'completed'
        // ]);
        // if ($updated) {
        //     //إشعار للمشاركين بانتهاء الفعالية
        
{
        $startingEvents = Event::where('status', 'upcoming')
                               ->whereDate('start_date', today())
                               ->get();

        if ($startingEvents->isNotEmpty()) {
    
            Event::whereIn('id', $startingEvents->pluck('id'))->update(['status' => 'ongoing']);

            
            foreach ($startingEvents as $event) {
                $tokens = $event->participations()->whereHas('user', function($q) {
                    $q->whereNotNull('fcm_token')
                    ->where('fcm_token', '!=', '');
                })->with('user')->get()->pluck('user.fcm_token')->unique();

                foreach ($tokens as $token) {
                    FirebaseService::sentNotification($token,
                     "انطلقت المسابقة! 🚀",
                     "بدأت فعالية {$event->event_name}. انطلقوا نحو التحدي!");
                }
            }
        }

        
        $endingEvents = Event::where('status', 'ongoing')
                             ->whereDate('end_date', today())
                             ->get();

        if ($endingEvents->isNotEmpty()) {
            
            Event::whereIn('id', $endingEvents->pluck('id'))->update(['status' => 'completed']);

            foreach ($endingEvents as $event) {
                
                $tokens = $event->participations()->whereHas('user', function($q) {
                    $q->whereNotNull('fcm_token')->where('fcm_token', '!=', '');
                })->with('user')->get()->pluck('user.fcm_token')->unique();

                foreach ($tokens as $token) {
                    FirebaseService::sentNotification($token,
                     "انتهت الفعالية! 🏁",
                     "شكراً لمشاركتكم في {$event->event_name}. ترقبوا النتائج!");
                }


          //  $events = Event::whereIn('id',$events_ids)->get();
            //foreach ($events as $event) {
                $winners_ids = Participation::where('event_id',$event->id)->where('status','finished')->pluck('user_id');
                $winners = User::whereIn('id',$winners_ids)->get();
                foreach($winners as $winner){
                    $user = User::where('id',$winner->id)->first();
                    $user->points = $user->points + $event->points;
                    $user->save();


                     if (!empty($winner->fcm_token)) {
                        $winnerTitle = "مبارك الفوز! 🏆🎉";
                        $winnerMessage = "ألف مبروك! لقد أنهيت فعالية \"{$event->event_name}\" بنجاح، وتمت إضافة {$event->points} نقطة إلى حسابك! 🌟";
                        
                        FirebaseService::sentNotification($winner->fcm_token, $winnerTitle, $winnerMessage);
                    }
                }
            }
        }

    }
}
