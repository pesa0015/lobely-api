<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Book;
use App\Bookshelf;
use Auth;
use Carbon\Carbon;

class BookController extends Controller
{
    public function save(Request $request)
    {
        $book_id = $request->book_id;

        $book = Bookshelf::where('user_id', Auth::user()->id)->where('book_id', $book_id)->get();
        
        if (!$book->isEmpty()) {
            return response()->json(['success' => false]);
        }
        
        $book = new Bookshelf;
        $book->user_id = Auth::user()->id;
        $book->book_id = $book_id;
        $book->timestamp = \Carbon\Carbon::now();
        $book->save();
        return response()->json(['success' => true, 'book_id' => $book_id]);
    }

    public function remove(Request $request)
    {
        $book_id = $request->book_id;

        $book = Bookshelf::where('user_id', Auth::user()->id)->where('book_id', $book_id)->first();
        
        if (!$book) {
            return response()->json(['success' => false]);
        }

        $book->delete();
        return response()->json(['success' => true, 'book_id' => $book_id]);
    }

    public function showBook($slug)
    {
        $book = Book::where('title_slug', $slug)->first();
        $saved = Bookshelf::where('user_id', Auth::user()->id)->where('book_id', $book->id)->first();
        $is_saved = false;
        if ($saved && $saved->user_id == Auth::user()->id) {
            $is_saved = true;
        }
        return view('book', ['book' => $book, 'saved' => $is_saved]);
    }
}
