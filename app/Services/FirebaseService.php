<?php

namespace App\Services;

use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Illuminate\Support\Facades\Log;
use App\Models\Notification as NotificationModel;
use App\Models\User;


class FirebaseService
{
    public static function sentNotification($token, $title, $body)
    {
        if (!$token) return;

        try {
            $messaging = app('firebase.messaging');
            $message = CloudMessage::withTarget('token', $token)
                ->withNotification(Notification::create($title, $body));


            $messaging->send($message);

                 $user = User::where('fcm_token', $token)->first();
            if ($user) {
                NotificationModel::create([
                    'user_id' => $user->id,
                    'title'   => $title,
                    'body'    => $body,
                ]);
            }

            return true;
        } catch (\Exception $e) {
         Log::error("FCM Error: " . $e->getMessage());
          return false;
        }
    }
}
