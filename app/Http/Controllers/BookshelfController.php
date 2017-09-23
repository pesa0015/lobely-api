<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreBookshelfRequest;
use App\Book;
use App\Bookshelf;

class BookshelfController extends TokenController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $books = Bookshelf::where('user_id', $this->user->id)->with('book')->get();

        return response()->json($books);
    }

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

        $haveLikedBook = Bookshelf::where([
            'user_id' => $this->user->id,
            'book_id' => $bookId
        ])->first();
        
        if ($haveLikedBook) {
            return response()->json(['already_liked_book' => true], 403);
        }
        
        $book = Bookshelf::create([
            'book_id' => $bookId,
            'user_id' => $this->user->id
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
        $book = Book::findOrFail($id);

        $bookshelf = Bookshelf::where([
            'user_id' => $this->user->id,
            'book_id' => $id
        ])->first();
        
        if (!$bookshelf) {
            return response()->json(['have_not_liked_book' => true], 403);
        }

        $bookshelf->delete();

        return response()->json([], 200);
    }
}