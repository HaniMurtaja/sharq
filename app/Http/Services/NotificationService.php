<?php
namespace App\Http\Services;

use App\Models\User;
use App\Repositories\NotificationRepository;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    protected $NotificationRepository;

    public function __construct(NotificationRepository $NotificationRepository)
    {
        $this->NotificationRepository = $NotificationRepository;
    }

    public function send($user_id, $title, $body, $redirect = null, $click = null, $icon = null)
    {
        // Get the Firebase tokens for the user
        $users = User::where('id', '=', $user_id)
            ->whereNotNull('firebase_token')
            ->pluck('firebase_token')
            ->all();

        $url = 'https://fcm.googleapis.com/v1/projects/alshrouqexpress-97ebd/messages:send';

        $accessToken = $this->getAccessToken();
        $headers     = [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];

        $data = [
            "message" => [
                "token"        => $accessToken,
                "notification" => [
                    "title" => $title,
                    "body"  => $body,
                ],
                'android'      => [
                    'notification' => [
                        'sound' => 'notification',
                    ],
                ],
                "data"         => [
                    "title"        => (string) $title,
                    "body"         => (string) $body,
                    "created_at"   => now()->format('Y-m-d g:i A'),
                    "redirect_id"  => (string) $redirect,
                    "click_action" => (string) $click,
                    "icon"         => (string) $icon,
                ],
            ],
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        if ($response === false) {
            throw new \Exception('FCM Send Error: ' . curl_error($ch));
        }
        curl_close($ch);

//        Log::info('FCM Response: ' . $response);

        $this->NotificationRepository->save($user_id, $title, $body, $redirect, $click, $icon);

        return $response;
    }

    public function sendOrderNotifications($user_id, $title, $body, $redirect = null, $click = null, $icon = null)
    {
        // Get the Firebase tokens for the user
        $firebaseTokens = User::where('id', '=', $user_id)
            ->whereNotNull('firebase_token')
            ->pluck('firebase_token')
            ->all();

        $url         = 'https://fcm.googleapis.com/v1/projects/alshrouqexpress-97ebd/messages:send';
        $accessToken = $this->getAccessToken();
        $headers     = [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];
//        $this->NotificationRepository->save($user_id, $title, $body, $redirect, $click, $icon);
        $user = User::findOrFail($user_id);
//        $notification_count = $user->unReaNotificationsCustom()->count();
        foreach ($firebaseTokens as $firebaseToken) {
            $data = [
                "message" => [
                    "token"        => $firebaseToken, // Use the actual Firebase token
                    "notification" => [
                        "title" => $title,
                        "body"  => $body,
                    ],
                    'android'      => [
                        'notification' => [
                            'sound' => 'notification',
                        ],
                    ],
                    "data"         => [
                        "title"              => (string) $title,
                        "body"               => (string) $body,
                        "created_at"         => now()->format('Y-m-d g:i A'),
                        "redirect_id"        => (string) $redirect,
                        "click_action"       => (string) $click,
                        "icon"               => (string) $icon,
                        'notification_count' => (string) 0,
                    ],
                ],
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

            $response = curl_exec($ch);
            if ($response === false) {
                throw new \Exception('FCM Send Error: ' . curl_error($ch));
            }
            curl_close($ch);

//            Log::info('FCM Response: ' . $response);
        }

        return 'Notifications sent successfully';
    }

    private function getAccessToken()
    {
        $client = new GoogleClient();
        $client->setAuthConfig(app_path() . '/Http/Controllers/Api/firebase.json');
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->useApplicationDefaultCredentials();

        return $client->fetchAccessTokenWithAssertion()['access_token'];
    }

    public function send_for_specific_users($title, $body, $ids, $redirect, $click, $icon)
    {
        $firebaseTokens = User::whereIn('id', $ids)->whereNotNull('firebase_token')->pluck('firebase_token')->all();
        $users          = User::whereIn('id', $ids)->whereNotNull('firebase_token')->pluck('id')->all();

        $url         = 'https://fcm.googleapis.com/v1/projects/alshrouqexpress-97ebd/messages:send';
        $accessToken = $this->getAccessToken();

        $headers = [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];

        foreach ($users as $user) {
            $this->NotificationRepository->save($user, $title, $body);
        }

        foreach ($firebaseTokens as $firebaseToken) {
            // Fetch the user associated with the current firebaseToken
            $user = User::where('firebase_token', $firebaseToken)->first();

            if (! $user) {
                continue; // Skip if the user is not found
            }

            $notification_count = $user->unReaNotificationsCustom()->count();
            $user_specific_ids  = $user->unReaNotificationsCustom()->pluck('user_id')->toArray(); // Renamed variable

            $data = [
                "message" => [
                    "token"        => $firebaseToken,
                    "notification" => [
                        "title" => $title,
                        "body"  => $body,
                    ],
                    "data"         => [
                        "title"              => (string) $title,
                        "body"               => (string) $body,
                        "created_at"         => now()->format('Y-m-d g:i A'),
                        "redirect_id"        => (string) $redirect,
                        "click_action"       => (string) $click,
                        "icon"               => (string) $icon,
                        'notification_count' => (string) $notification_count,
                        "token"              => $firebaseToken,
                        'ids'                => json_encode($user_specific_ids), // Use user-specific IDs
                    ],
                ],
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

            $response = curl_exec($ch);
            if ($response === false) {
                throw new \Exception('FCM Send Error: ' . curl_error($ch));
            }
            curl_close($ch);

//            Log::info('FCM Response: ' . $response);
        }
        return 'Notifications sent successfully';
    }

    public function send_for_specific($title, $body, $id): string
    {
        $firebaseTokens = User::where('id', $id)->first();
//        $users = User::whereIn('id', $ids)->whereNotNull('firebase_token')->pluck('id')->all();

        $url         = 'https://fcm.googleapis.com/v1/projects/alshrouqexpress-97ebd/messages:send';
        $accessToken = $this->getAccessToken();

        $headers = [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];

//        foreach ($users as $user) {
//            $this->NotificationRepository->save($user, $title, $body);
//        }

//        foreach ($firebaseTokens as $firebaseToken) {
        // Fetch the user associated with the current firebaseToken
        //    $user = User::where('firebase_token', $firebaseToken)->first();
//
//            if (!$user) {
//                continue; // Skip if the user is not found
//            }
//
//            $notification_count = $user->unReaNotificationsCustom()->count();
//            $user_specific_ids = $user->unReaNotificationsCustom()->pluck('user_id')->toArray(); // Renamed variable

        if (! $firebaseTokens) {
            goto End;
        }
        $data = [
            "message" => [
                "token"        => $firebaseTokens->firebase_token,
                "notification" => [
                    "title" => $title,
                    "body"  => $body,
                ],
                'android'      => [
                    'priority'     => 'high',
                    'notification' => [
                        'sound'                 => 'notification',
                        "channel_id"            => "channel_id",
                        "notification_priority" => 'PRIORITY_HIGH',
                    ],
                ],
                "data"         => [
                    "title"      => (string) $title,
                    "body"       => (string) $body,
                    "sound"      => "notification",
                    "created_at" => now()->format('Y-m-d g:i A'),
                ],
                'apns'         => [
                    'headers' => [
                        'apns-priority' => '10', // High priority for iOS
                    ],
                    'payload' => [
                        'aps' => [
                            'alert' => [
                                'title' => $title,
                                'body'  => $body,
                            ],
                            'sound' => 'notification.caf', // Use 'default' for standard notification sound
                            'badge' => 2,                  // Badge count
                        ],
                    ],
                ],
            ],
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        if ($response === false) {
            throw new \Exception('FCM Send Error: ' . curl_error($ch));
        }
        curl_close($ch);

        Log::info('FCM Response: ' . $response);
//        }

        End:
        return 'Notifications sent successfully';
    }
}
