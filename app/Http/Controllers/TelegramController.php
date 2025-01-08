<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class TelegramController extends Controller
{
    private $token;
    
    public function __construct()
    {
        $this->token = '7447111827:AAGFjzabdTKK_Qoy302x4SeFNsD6Kaxr-RE';
    }

    public function sendTelegram(Request $request)
    {
        try {
            $data = $request->all();
            
            if (!isset($data['message']['text'], $data['message']['chat']['id'])) {
                return response()->json(['status' => 'error', 'message' => 'Invalid Telegram data'], 400);
            }

            $text = $data['message']['text'];
            $chat_id = $data['message']['chat']['id'];
            
            $this->handleGuessingGame($chat_id, $text);
            Log::info('Telegram webhook received:', $data);
            
            return response()->json(['status' => 'success'], 200);
            
        } catch (\Exception $e) {
            Log::error('Telegram Webhook error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    private function handleGuessingGame($chat_id, $text)
    {
        $cacheKey = "telegram_game_{$chat_id}";
        $game = Cache::get($cacheKey);

        if (!$game) {
            $game = [
                'number_to_guess' => rand(1, 100),
                'attempts' => 0,
                'expires_at' => now()->addHours(1)
            ];
            Cache::put($cacheKey, $game, $game['expires_at']);
            $this->sendMessage($chat_id, 'I have picked a number between 1 and 100. Try to guess it!');
            return;
        }

        if (!is_numeric($text)) {
            $this->sendMessage($chat_id, "Please enter a valid number between 1 and 100.");
            return;
        }

        $game['attempts']++;
        $guess = (int)$text;

        if ($guess === $game['number_to_guess']) {
            $this->sendMessage($chat_id, "🎉 Congratulations! You guessed the number in {$game['attempts']} attempts.");
            Cache::forget($cacheKey);
        } elseif ($guess < $game['number_to_guess']) {
            $this->sendMessage($chat_id, "Too low! Try again.");
            Cache::put($cacheKey, $game, $game['expires_at']);
        } else {
            $this->sendMessage($chat_id, "Too high! Try again.");
            Cache::put($cacheKey, $game, $game['expires_at']);
        }
    }

    private function sendMessage($chat_id, $text)
    {
        $response = Http::post("https://api.telegram.org/bot{$this->token}/sendMessage", [
            'chat_id' => $chat_id,
            'text' => $text,
            'parse_mode' => 'HTML'
        ]);

        if ($response->failed()) {
            Log::error('Failed to send Telegram message', [
                'response' => $response->body(),
                'chat_id' => $chat_id
            ]);
            throw new \Exception('Telegram API Error: ' . $response->body());
        }
    }
}