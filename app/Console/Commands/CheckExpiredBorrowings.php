<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Borrowing;
use App\Services\FirebaseService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckExpiredBorrowings extends Command
{
   
    protected $signature = 'app:check-expired-borrowings';

    
    protected $description = 'expired-borrowings';

    
    public function handle()
    {

    //Log::info("يتم فحص الاستعارات المنتهية");
           $expiredBorrowings = Borrowing::with(['user', 'book'])
            ->where('status', 'active')
            ->where('expires_at', '<=', Carbon::now())
            ->get();

        foreach ($expiredBorrowings as $borrowing) {
            
            $borrowing->update(['status' => 'expired']);

            
            $user = $borrowing->user;
            $book = $borrowing->book;

            if ($user && !empty($user->fcm_token)) {
                FirebaseService::sentNotification(
                    $user->fcm_token,
                    'انتهت فترة الاستعارة 🔒',
                    "انتهت مدة استعارتك لكتاب \"{$book->title}\" وتم إغلاقه الآن. نتمنى لك رحلة قراءة ممتعة وننتظرك في كتاب جديد!"
                );
            }
        }
    }
}
