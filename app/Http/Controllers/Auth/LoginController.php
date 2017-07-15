<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
// use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\User;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
        $this->middleware('jwt.auth', ['except' => 'loginWithFacebook']);
    }

    public function loginWithFacebook(Request $request)
    {
        $user = User::where('facebook_id', $request->facebook_id)->first();

        if (!$user) {
            $user = User::create([
                'facebook_id' => $request->facebook_id,
                'name' => $request->name,
                'email' => $request->email,
                'gender' => $request->gender
                // 'password' => bcrypt($request->password)
            ]);
        }
        
        $token = JWTAuth::fromUser($user);

        return response()->json(compact('token'));
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json([], 200);
    }
}
