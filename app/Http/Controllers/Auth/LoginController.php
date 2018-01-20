<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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

    public function loginWithFacebook(Request $request)
    {
        $user = User::where('facebook_id', $request->facebook_id)->first();

        if (!$user) {
            $user = User::create([
                'facebook_id' => $request->facebook_id,
                'slug' => User::generateSlug($request->name),
                'name' => $request->name,
                'email' => $request->email,
                'gender' => $request->gender
            ]);
        }
        
        $token = JWTAuth::fromUser($user, ['firstname' => $user->getFirstName()]);

        return response()->json(compact('token'));
    }

    public function authenticate(Request $request)
    {
        // grab credentials from the request
        $credentials = $request->only('email', 'password');

        try {
            $user = User::where('email', $credentials['email'])->first();

            $firstname = $user ? $user->getFirstName() : null;

            // attempt to verify the credentials and create a token for the user
            if (!$token = JWTAuth::attempt($credentials, ['firstname' => $firstname])) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        $user = User::where('email', $credentials['email'])->first();

        $firstname = explode(' ', $user->name)[0];

        // all good so return the token
        return response()->json(compact('token'));
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json([], 200);
    }
}
