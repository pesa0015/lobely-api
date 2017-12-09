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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $userRaw = User::findOrFail($this->user->id);

        $user = $this->transform->item($userRaw, User::getTransformer());

        return response()->json($user);
    }
}
