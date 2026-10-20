<?php

namespace App\Traits;

use App\Models\Notification;
use App\Models\NotificationSeen;

trait SendNotification
{
    public function send($text, $type, $target_url, $user_id = null)
    {
        $notification = new Notification();
        $notification->body = $text;
        $notification->link = $target_url;
        if ($user_id) {
            $notification->user_id = $user_id;
        }
        $notification->save();

        if ($user_id) {
            $notificationSeen = new NotificationSeen();
            $notificationSeen->notification_id = $notification->id;
            $notificationSeen->user_id = $user_id;
            $notificationSeen->save();
        }
    }
}
