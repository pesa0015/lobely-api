<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\PasswordReset;
use Illuminate\Support\Facades\Mail;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    public function update(Request $request)
    {
        $token = PasswordReset::where('token', $request->token)->firstOrFail();

        $user = User::findOrFail($token->user_id);
        $user->password = bcrypt($request->password);
        $user->update();

        $token->delete();

        Mail::send('emails.reset-password', [], function ($message)
        use ($user)
        {
            $message->from(env('MAIL_ADDRESS'), env('MAIL_NAME'));
            $message->to($user->email);
        });

        return response()->json([], 200);
    }
}
