<?php

namespace App\Http\Controllers\Auth;

use Auth;
use App\User;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Redirect;

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
    }

    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return Response
     */
    public function redirectToProvider()
    {
        return \Socialite::driver('facebook')->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return Response
     */
    public function handleProviderCallback()
    {
        $user = \Socialite::driver('facebook')->user();

        // OAuth Two Providers
        $token = $user->token;
        $expiresIn = $user->expiresIn;

        $userExists = User::where('facebook_id', $user->id)->first();

        if ($userExists) {
            $userExists->name = $user->getName();
            $userExists->email = $user->getEmail();
            $userExists->gender = $user->user['gender'];
            $userExists->update();

            Auth::login($userExists);
        }
        else {
            $newUser = new User;

            $newUser->name = $user->getName();
            $newUser->email = $user->getEmail();
            $newUser->gender = $user->user['gender'];
            $newUser->facebook_id = $user->getId();
            $newUser->save();

            Auth::login($newUser);
        }

        return Redirect::to('bookshelf');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function logout()
    {
        Auth::logout();
        return Redirect::to('/');
    }
}
