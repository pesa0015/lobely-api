<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreBookshelfRequest;
use App\Token\UserFromToken;
use App\Book;
use App\Bookshelf;

class BookshelfController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBookshelfRequest $request)
    {
        $bookId = $request->bookId;

        $book = Book::findOrFail($bookId);

        $user = UserFromToken::get();

        $haveLikedBook = Bookshelf::where([
            'user_id' => $user->id,
            'book_id' => $bookId
        ])->first();
        
        if ($haveLikedBook) {
            return response()->json('already_liked_book', 403);
        }
        
        $book = Bookshelf::create([
            'book_id' => $bookId,
            'user_id' => $user->id
        ]);

        return response()->json([], 200);
    }
}
