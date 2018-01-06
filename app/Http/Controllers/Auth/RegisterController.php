<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CreateUserWithoutFacebookRequest as CreateUserRequest;

class RegisterController extends Controller
{
    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    public function store(CreateUserRequest $request)
    {
        $user = User::create([
            'facebook_id' => 0,
            'name'        => $request->name,
            'slug'        => User::generateSlug($request->name),
            'email'       => $request->email,
            'gender'      => $request->gender,
            'password'    => bcrypt($request->password)
        ]);

        return response()->json($user);
    }
}
