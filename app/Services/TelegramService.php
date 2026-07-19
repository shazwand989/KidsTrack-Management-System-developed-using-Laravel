<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TelegramService
{
    protected $botToken;
    protected $apiUrl;

    public function __construct()
    {
        $this->botToken = env('TELEGRAM_BOT_TOKEN');
        $this->apiUrl = "https://api.telegram.org/bot{$this->botToken}/";
    }

    public function sendMessage($chatId, $message)
    {
        try {
            $response = Http::post($this->apiUrl . 'sendMessage', [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
            ]);

            return $response->json();
        } catch (\Exception $e) {
            \Log::error('Telegram Error: ' . $e->getMessage());
            return null;
        }
    }
}