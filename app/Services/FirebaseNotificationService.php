<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FirebaseNotificationService
{
    protected $messaging;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(
                config('firebase.credentials')
            );

        $this->messaging = $factory->createMessaging();
    }

    public function send($tokens, $title, $body, $url)
    {
		   \Log::info('Total Tokens: ' . count($tokens));
        foreach ($tokens as $token) {
              \Log::info('Sending to: ' . substr($token, 0, 30));
            $message = CloudMessage::withTarget(
                'token',
                $token
            )
            // ->withNotification(
                // Notification::create(
                    // $title,
                    // $body
                // )
            // )
            // ->withData([
                // 'url' => $url
            // ]);
			 ->withData([
				'title' => $title,
				'body'  => $body,
				'url'   => $url,
			]);

            try {
                $this->messaging->send($message);
            } catch (\Exception $e) {
				\Log::error($e->getMessage());
            }
        }
    }
}