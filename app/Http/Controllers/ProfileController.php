<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\User;

class ProfileController extends CustomController
{
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProfileRequest $request)
    {
        $userRaw = User::findOrFail($this->user->id);

        $email = User::where('email', $request->email)->where('id', '!=', $userRaw->id)->get();

        if (!$email->isEmpty()) {
            return response()->json(['email' => ['The email has already been taken.']], 422);
        }

        $userRaw->update($request->all());

        $user = $this->transform->item($userRaw, User::getTransformer());

        return response()->json($user);
    }

    /**
     * Update user password
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(UpdatePasswordRequest $request)
    {
        $currentPassword   = $request->current;

        $credentials = ['email' => $this->user->email, 'password' => $currentPassword];

        if (!\JWTAuth::attempt($credentials)) {
            return response()->json('user_not_allowed', 403);
        }

        $new       = $request->new;
        $repeatNew = $request->repeatNew;

        if ($new != $repeatNew) {
            return response()->json('password_incorrect', 403);
        }

        $user = User::findOrFail($this->user->id);
        $user->password = bcrypt($new);
        $user->update();

        return response()->json([], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $userRaw = User::findOrFail($this->user->id);

        $user = $this->transform->item($userRaw, User::getTransformer());

        return response()->json($user);
    }
}
