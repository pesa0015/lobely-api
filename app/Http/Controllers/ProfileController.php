<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
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
    public function update(UpdateProfileRequest $request, $id)
    {
        $user = User::findOrFail($this->user->id);

        $email = User::where('email', $request->email)->where('id', '!=', $user->id)->get();

        if (!$email->isEmpty()) {
            return response()->json(['email' => ['The email has already been taken.']], 422);
        }

        $user->update($request->all());

        return response()->json($user);
    }
}
