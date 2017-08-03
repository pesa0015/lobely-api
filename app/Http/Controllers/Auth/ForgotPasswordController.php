<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\PasswordReset;
use App\Mail\CustomMail;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    public function store(Request $request)
    {
        $user = User::where('email', $request->email)->firstOrFail();

        $token = str_random(20);

        PasswordReset::create([
            'user_id' => $user->id,
            'token'   => $token
        ]);

        $url = env('APP_URL') . '/reset-password';

        $mail = new CustomMail();
        $mail->send('emails.forgot-password', $user->email, ['url' => $url, 'token' => $token]);
    }
}
