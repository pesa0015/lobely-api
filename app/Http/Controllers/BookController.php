<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Book;
use App\Bookshelf;
use App\User;
use Auth;
use Carbon\Carbon;

class BookController extends CustomController
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

    public function show($slug)
    {
        $bookRaw = Book::where('slug', $slug)->firstOrFail();

        $onMyBookshelf = $bookRaw->users()->where('user_id', $this->user->id)->exists();

        $book = $this->transform->item($bookRaw, Book::getTransformer(), Book::getIncludes());
        
        if ($onMyBookshelf) {
            $book->liked = true;
        } else {
            $book->liked = false;
        }

        return response()->json($book);
    }
}
