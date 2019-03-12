<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreHeartRequest;
use App\Http\Requests\UpdateHeartRequest;
use App\Constants\HeartStatus;
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

        if ($this->user->heartsToPartner()->forUser($user->id)->exists()) {
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateHeartRequest $request, $userId)
    {
        $user = User::findOrFail($userId);

        $heartRaw = Heart::where([
            'user_id' => $user->id,
            'heart_user_id' => $this->user->id,
            'status'  => HeartStatus::PENDING
        ])->firstOrFail();

        $heartRaw->update([
            'status'    => $request->status,
            'have_read' => true
        ]);

        $transformer = new \App\Http\Transformer\HeartTransformer;

        $heart = $this->transform->item($heartRaw, $transformer);

        return response()->json($heart, 200);
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

        $fromPartner = $this->user->heartsToMe()->forUser($user->id);

        if ($fromPartner->exists()) {
            $fromPartner->denied();

            return response()->json([], 200);
        }

        $toPartner = $this->user->heartsToPartner()->forUser($user->id);

        if ($toPartner->exists()) {
            if ($toPartner->status === HeartStatus::PENDING) {
                $toPartner->delete();
            } else {
                $toPartner->denied();
            }

            return response()->json([], 200);
        }
        
        return response()->json('have_not_liked_user', 403);
    }
}
