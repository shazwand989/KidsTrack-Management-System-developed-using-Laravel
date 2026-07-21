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

    /**
     * Send notification to admin via TELEGRAM_ADMIN_CHAT_ID env.
     */
    public function sendToAdmin($message)
    {
        $adminChatId = env('TELEGRAM_ADMIN_CHAT_ID');
        if (!$adminChatId) {
            \Log::warning('Telegram: TELEGRAM_ADMIN_CHAT_ID not set in .env');
            return null;
        }
        return $this->sendMessage($adminChatId, $message);
    }
}
