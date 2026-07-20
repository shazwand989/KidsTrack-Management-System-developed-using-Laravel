<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $update = $request->all();

        // Only handle messages
        if (!isset($update['message'])) {
            return response()->json(['status' => 'ok']);
        }

        $message = $update['message'];
        $chatId = $message['chat']['id'] ?? null;
        $text = $message['text'] ?? '';
        $firstName = $message['from']['first_name'] ?? 'User';

        if (!$chatId) {
            return response()->json(['status' => 'ok']);
        }

        $telegram = new TelegramService();

        // Handle /start command
        if (str_starts_with($text, '/start')) {
            // Check if already linked
            $existing = User::where('telegram_chat_id', $chatId)->first();
            if ($existing) {
                $telegram->sendMessage($chatId,
                    "✅ Hello {$firstName}! You are already linked as *{$existing->name}*.\n\nYou will receive late check-in/check-out notifications here.\n\nType /status to see today's attendance."
                );
                return response()->json(['status' => 'ok']);
            }

            $telegram->sendMessage($chatId,
                "👋 Hello {$firstName}! Welcome to *KIDSTRACK SAFECARE*!\n\n"
                . "To link your account, please enter your *phone number*:\n\n"
                . "Example: 0122424534\n\n"
                . "We'll match it to your parent account."
            );
            return response()->json(['status' => 'ok']);
        }

        // Handle /status command
        if (str_starts_with($text, '/status')) {
            $user = User::where('telegram_chat_id', $chatId)->first();
            if (!$user) {
                $telegram->sendMessage($chatId, "❌ Please link your account first with /start");
                return response()->json(['status' => 'ok']);
            }
            $this->sendStatusReport($chatId, $user, $telegram);
            return response()->json(['status' => 'ok']);
        }

        // Handle /help command
        if (str_starts_with($text, '/help')) {
            $telegram->sendMessage($chatId,
                "📋 *KIDSTRACK Commands*\n\n"
                . "/start - Link your parent account\n"
                . "/status - Check today's attendance\n"
                . "/help - Show this help\n\n"
                . "For assistance, contact the nursery."
            );
            return response()->json(['status' => 'ok']);
        }

        // Handle phone number input (after /start)
        $phone = preg_replace('/[\s\-]/', '', $text);
        if (preg_match('/^[0-9]{10,12}$/', $phone)) {
            $linked = $this->linkUserByPhone($chatId, $phone, $telegram);
            return response()->json(['status' => 'ok']);
        }

        // Unknown command
        $telegram->sendMessage($chatId,
            "🤔 I didn't understand that. Type /start to begin."
        );

        return response()->json(['status' => 'ok']);
    }

    private function linkUserByPhone($chatId, $phone, TelegramService $telegram)
    {
        // Search in users table for parent role
        $parent = User::whereIn('role', ['parent', 'parent1'])
            ->where('phone_number', 'like', '%' . substr($phone, -7) . '%')
            ->first();

        if ($parent) {
            $parent->update(['telegram_chat_id' => $chatId]);
            $telegram->sendMessage($chatId,
                "✅ Account linked! Welcome *{$parent->name}*!\n\n"
                . "You will now receive late check-in/check-out notifications for your child(ren).\n\n"
                . "Commands:\n"
                . "/status - Check today's attendance\n"
                . "/help - Show help"
            );
            return true;
        }

        // Search in users table for guardian role
        $guardian = User::where('role', 'guardian')
            ->where('phone_number', 'like', '%' . substr($phone, -7) . '%')
            ->first();
        if ($guardian) {
            $guardian->update(['telegram_chat_id' => $chatId]);
            $telegram->sendMessage($chatId,
                "✅ Account linked! Welcome *{$guardian->name}*!\n\n"
                . "You will now receive notifications for your linked child(ren)."
            );
            return true;
        }

        $telegram->sendMessage($chatId,
            "❌ Phone number not found in our records.\n\n"
            . "Please make sure this phone number matches the one registered with the nursery."
        );
        return false;
    }

    private function sendStatusReport($chatId, $user, TelegramService $telegram)
    {
        if (!in_array($user->role, ['parent', 'parent1', 'parent2', 'guardian'])) {
            $telegram->sendMessage($chatId, "❌ No parent account linked.");
            return;
        }

        $today = \App\Models\SimulationClock::getCurrentTime();
        $todayDate = date('Y-m-d', $today);
        $children = $user->children;

        if ($children->isEmpty()) {
            $telegram->sendMessage($chatId, "👶 No children registered under your account.");
            return;
        }

        $msg = "📊 *Today's Attendance ({$todayDate})*\n\n";
        foreach ($children as $child) {
            $att = \App\Models\Attendance::where('child_id', $child->id)
                ->where('date', $todayDate)
                ->first();

            $status = '❓ Unknown';
            $emoji = '❓';
            if (!$att) {
                $status = 'Not checked in';
                $emoji = '⏰';
            } elseif (in_array($att->status, ['checkin', 'present'])) {
                $status = 'Checked in at ' . date('h:i A', strtotime($att->checkin_time));
                $emoji = '✅';
            } elseif ($att->status == 'late') {
                $status = 'LATE at ' . date('h:i A', strtotime($att->checkin_time));
                $emoji = '⚠️';
            } elseif ($att->status == 'checkout') {
                $status = 'Checked out at ' . date('h:i A', strtotime($att->checkout_time));
                $emoji = '📤';
            } elseif ($att->status == 'late_checkout') {
                $status = 'LATE checkout at ' . date('h:i A', strtotime($att->checkout_time));
                $emoji = '⚠️';
            }

            $msg .= "{$emoji} *{$child->name}*\n   {$status}\n\n";
        }

        $telegram->sendMessage($chatId, $msg);
    }
}
