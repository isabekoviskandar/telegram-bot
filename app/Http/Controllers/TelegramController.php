<?php

namespace App\Http\Controllers;

use App\Models\Workers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TelegramController extends Controller
{
    public $workers;

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
        $workers = Workers::all();
        $message = "Ishchilar:\n";
        foreach ($workers as $worker) {
            $message .= "Ismi: {$worker->first_name},Familiyasi: {$worker->last_name} ,
            Yoshi: {$worker->age}, Telefon raqami: {$worker->phone_number}";
        }
        $response = Http::post($token . '/sendMessage' , [
            'parse_mode' => 'HTML',
            'chat_id' => '6784560209',
            'text' => $message,
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        ['text' => 'Ishchilar' , 'callback_data' => 'button'],
                    ],
                ],
                'resize_keyboard' => true, 
            ])
        ]);

        return back()->with('success' , 'Keli otam');
    }
}
