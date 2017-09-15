<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Book;
use App\Bookshelf;
use App\User;
use Auth;
use Carbon\Carbon;

class BookController extends Controller
{
    public function index(Request $request)
    {
        if (isset($request->title)) {
            $title = $request->title;
            if (strlen($title) >= 2) {
                $books = Book::where('title', 'LIKE', "%{$title}%")->limit(5)->get();
                return response()->json($books);
            }
        }
        return response()->json([], 200);
    }

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

    public function show($slug)
    {
        $book = Book::where('slug', $slug)->firstOrFail();
        
        return response()->json($book);
    }
}
