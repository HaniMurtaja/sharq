<?php

namespace App\Notifications;



use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class NewOrderNotification extends Notification
{
  

    /**
     * Create a new notification instance.
     */
   
    public function via($notifiable)
    {
        return ['fcm', 'database'];
    }

    public function toFcm($notifiable)
    {
        return FcmMessage::create()
            ->setData([
                'key' => 'value',
            ])
            ->setNotification(
                FcmNotification::create()
                    ->setTitle('New Order')
                    ->setBody('New Order Created')
                    ->setImage('')
            );
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'New Order',
            'body' => 'New Order Created',
            'url' => '',
        ];
    }
}
