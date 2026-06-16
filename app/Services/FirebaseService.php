<?php

namespace App\Services;

use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

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
            return true;
        } catch (\Exception $e) {

            return false;
        }
    }
}
