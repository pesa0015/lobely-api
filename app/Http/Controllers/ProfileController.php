<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        $credentials = ['email' => $this->user->email, 'password' => $request->currentPassword];

        if (!\JWTAuth::attempt($credentials)) {
            return response()->json('user_not_allowed', 403);
        }

        $new       = $request->newPassword;
        $repeatNew = $request->repeatNewPassword;

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
    public function me()
    {
        $userRaw = User::findOrFail($this->user->id);

        $user = $this->transform->item($userRaw, User::getTransformer());

        return response()->json($user);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $slug)
    {
        $foundUser = User::where('slug', $slug)->firstOrFail();

        if ($request->filled('book')) {
            $book = \App\Book::where('slug', $request->book)->firstOrFail();

            $userRaw = $foundUser->load(['books' => function ($query) use ($foundUser, $book) {
                return $query->where('user_id', $foundUser->id)->where('book_id', $book->id);
            }]);

            $this->transform->setBook($book);
            $includes = ['books', 'book', 'like'];
        } else {
            $userRaw  = $foundUser;
            $includes = ['books'];
        }

        $user = $this->transform->item($userRaw, User::getTransformer(), $includes);

        return response()->json($user);
    }
}
