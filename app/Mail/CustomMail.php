<?php

namespace App\Mail;

use Illuminate\Support\Facades\Mail;

class CustomMail
{
    public function send($view, $to, array $data = [])
    {
        Mail::send($view, $data, function ($message) use ($to) {
            $message->from(env('MAIL_ADDRESS'), env('MAIL_NAME'));
            $message->to($to);
        });
    }
}
