<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreHeartRequest;
use App\User;
use App\Heart;
use App\Book;

class HeartController extends CustomController
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreHeartRequest $request)
    {
        $user = User::findOrFail($request->userId);

        $book = Book::findOrFail($request->bookId);

        if (!$this->user->haveLikedBook($book->id)) {
            return response()->json('user_have_not_liked_book', 403);
        }

        if (!\App\Bookshelf::partnerHaveLikedBook($book->id, $user->id)) {
            return response()->json('partner_have_not_liked_book', 403);
        }

        if ($this->user->heartsToPartner()->exists()) {
            return response()->json('already_have_heart', 403);
        }

        Heart::create([
            'user_id'       => $this->user->id,
            'heart_user_id' => $user->id,
            'book_id'       => $book->id
        ]);

        return response()->json([], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        $fromPartner = $this->user->heartsToMe()->where('user_id', $user->id);

        if ($fromPartner->exists()) {
            $fromPartner->first()->pivot->delete();

            return response()->json([], 200);
        }

        $toPartner = $this->user->heartsToPartner()->where('heart_user_id', $user->id);

        if ($toPartner->exists()) {
            $toPartner->first()->pivot->delete();

            return response()->json([], 200);
        }
        
        return response()->json('have_not_liked_user', 403);
    }

    public function notifications()
    {
        $users = $this->user->heartsToMe()->where('have_read', false)->with('book')->get();

        $transformer = new \App\Http\Transformer\UserTransformer;

        $notifications = $this->transform->collection($users, $transformer, ['book']);

        return response()->json($notifications);
    }

    public function countNotifications()
    {
        $count = $this->user->heartsToMe()->where('have_read', false)->count();

        return response()->json(['count' => $count]);
    }
}
