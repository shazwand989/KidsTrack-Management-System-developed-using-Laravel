<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected string $botToken;
    protected string $apiUrl;

    public function __construct()
    {
        $this->botToken = env('TELEGRAM_BOT_TOKEN', '');
        $this->apiUrl = "https://api.telegram.org/bot{$this->botToken}/";
    }

    public function sendMessage(string $chatId, string $message): ?array
    {
        try {
            $response = Http::post($this->apiUrl . 'sendMessage', [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Telegram Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Send notification to admin via TELEGRAM_ADMIN_CHAT_ID env.
     */
    public function sendToAdmin(string $message): ?array
    {
        $adminChatId = env('TELEGRAM_ADMIN_CHAT_ID');
        if (!$adminChatId) {
            Log::warning('Telegram: TELEGRAM_ADMIN_CHAT_ID not set in .env');
            return null;
        }
        return $this->sendMessage($adminChatId, $message);
    }
}
