<?php

namespace App\Http\Controllers;

use App\Services\TelegramBotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramBotController extends Controller
{
    public function webhook(Request $request, TelegramBotService $bot)
    {
        // Verify secret token if configured
        $secret = config('telegram.webhook_secret');
        if ($secret) {
            $token = $request->header('X-Telegram-Bot-Api-Secret-Token');
            if ($token !== $secret) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        }

        try {
            $bot->handleUpdate($request->all());
        } catch (\Exception $e) {
            Log::error('Telegram webhook error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        // Always return 200 to Telegram
        return response()->json(['ok' => true]);
    }
}
