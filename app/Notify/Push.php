<?php
namespace App\Notify;

use GuzzleHttp\Client;

class Push extends NotifyProcess
{
    protected function prevConfiguration()
    {
        $this->body = 'push_body';
        $this->statusField = 'push_status';
        $this->globalTemplate = 'push_template';
        $this->notifyConfig = 'firebase_config';
        $this->sentFrom = 'Firebase';
    }

    public function send()
    {
        $message = $this->getMessage();

        if (!$message) {
            return false;
        }

        try {
            return $this->sendViaFcm();
        } catch (\Exception $e) {
            $this->createErrorLog('Push notification failed: ' . $e->getMessage());
            $this->createLog('push', false);
            return false;
        }
    }

    public function sendBulk(array $notifications)
    {
        $config = json_decode(gs('firebase_config'), true);
        $serverKey = $config['server_key'] ?? null;

        if (!$serverKey) {
            throw new \Exception('Firebase server key not configured');
        }

        $client = new Client([
            'base_uri' => 'https://fcm.googleapis.com/fcm/',
            'headers' => [
                'Authorization' => 'key=' . $serverKey,
                'Content-Type' => 'application/json',
            ],
        ]);

        $messages = [];
        foreach ($notifications as $n) {
            $messages[] = [
                'to' => $n['fcm_token'],
                'notification' => [
                    'title' => $n['subject'],
                    'body' => strip_tags($n['message']),
                    'image' => $n['image'] ?? null,
                ],
                'data' => $n['data'] ?? [],
            ];
        }

        // Send in one batch
        foreach ($messages as $msg) {
            $client->post('send', ['json' => $msg]);
        }
    }

    private function sendViaFcm()
    {
        $config = json_decode(gs('firebase_config'), true);
        $serverKey = $config['server_key'] ?? null;

        if (!$serverKey) {
            throw new \Exception('Firebase server key not configured');
        }

        $fcmToken = $this->user->fcm_token ?? null;
        if (!$fcmToken) {
            throw new \Exception('FCM token not found for user');
        }

        $client = new Client([
            'base_uri' => 'https://fcm.googleapis.com/fcm/',
            'headers' => [
                'Authorization' => 'key=' . $serverKey,
                'Content-Type' => 'application/json',
            ],
        ]);

        $payload = [
            'to' => $fcmToken,
            'notification' => [
                'title' => $this->subject,
                'body' => strip_tags($this->finalMessage),
                'image' => $this->pushImage,
            ],
            'data' => $this->shortCodes ?? [],
        ];

        $response = $client->post('send', ['json' => $payload]);

        $success = $response->getStatusCode() === 200;
        $this->createLog('push', $success);
        return $success;
    }
}
