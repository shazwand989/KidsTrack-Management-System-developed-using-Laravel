<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TelegramService;
use App\Models\ParentModel;

class TelegramPolling extends Command
{
    protected $signature = 'telegram:poll';
    protected $description = 'Poll Telegram updates';

    protected $telegram;
    protected $lastUpdateId = 0;

    public function __construct(TelegramService $telegram)
    {
        parent::__construct();
        $this->telegram = $telegram;
    }

    public function handle()
    {
        $this->info('🚀 Telegram polling started...');

        while (true) {
            try {
                $updates = $this->getUpdates();

                foreach ($updates as $update) {
                    if (isset($update['message'])) {
                        $chatId = $update['message']['chat']['id'];
                        $text = $update['message']['text'] ?? '';

                        // Cari email dalam message
                        preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $text, $matches);
                        
                        if (isset($matches[0])) {
                            $email = $matches[0];
                            
                            // Cari parent
                            $parent = ParentModel::whereHas('user', function($query) use ($email) {
                                $query->where('email', $email);
                            })->first();

                            if ($parent) {
                                $parent->update([
                                    'telegram_id' => $chatId,
                                    'telegram_notification' => true,
                                ]);

                                $this->telegram->sendMessage($chatId, "✅ Email registered successfully! You will receive notifications.");
                                $this->info("✅ Registered: $email");
                            } else {
                                $this->telegram->sendMessage($chatId, "❌ Email not found. Please use your registered email.");
                                $this->info("❌ Email not found: $email");
                            }
                        } else {
                            $this->telegram->sendMessage($chatId, "📧 Please send your registered email to activate notifications.\n\nExample: farhana@gmail.com");
                        }
                    }

                    $this->lastUpdateId = $update['update_id'] + 1;
                }

            } catch (\Exception $e) {
                $this->error('Error: ' . $e->getMessage());
            }

            sleep(2);
        }
    }

    private function getUpdates()
    {
        $response = \Illuminate\Support\Facades\Http::get(
            "https://api.telegram.org/bot" . env('TELEGRAM_BOT_TOKEN') . "/getUpdates",
            [
                'offset' => $this->lastUpdateId,
                'timeout' => 10,
            ]
        );

        return $response->json()['result'] ?? [];
    }
}