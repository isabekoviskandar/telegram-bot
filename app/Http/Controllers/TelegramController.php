<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TelegramController extends Controller
{

    public function index()
    {
        return view('telegram');
    }
    public function store(Request $request)
    {
        $token = 'https://api.telegram.org/bot7447111827:AAGFjzabdTKK_Qoy302x4SeFNsD6Kaxr-RE';
        $date = $request->validate([
            'text' => 'required',
        ]);

        $response = Http::post($token . '/sendMessage' , [
            'parse_mode' => 'HTML',
            'chat_id' => '6784560209',
            'text' => $date['text'],
            'reply_markup' => json_encode([
                'keyboard' => [
                    [
                        ['text' => 'Iskandar'],
                    ],
                    [
                        ['text' => 'Iskandar'] , ['text' => 'Ravshanbek'],
                    ],
                    [
                        ['text' => 'Iskandar'] , ['text' => 'Ravshanbek'] , ['text' => 'Abdulaziz'],
                    ],
                ],
                'resize_keyboard' => true, 
            ])
        ]);

        return back()->with('success' , 'Keli otam');
    }
}
