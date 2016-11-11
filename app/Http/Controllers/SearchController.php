<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use Validator;
use App\Book;

class SearchController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function search()
    {
        $validator = Validator::make(Input::all(), [
            'search' => 'required'
        ]);

        if ($validator->fails()) 
            return Redirect::to('bookshelf');

        $title = Input::get('search');
        $books = Book::select('books.id', 'title', 'title_slug', 'bookshelves.user_id')
                     ->where('title', 'LIKE', "%{$title}%")
                     ->leftJoin('bookshelves', 'books.id', '=', 'bookshelves.book_id')
                     ->limit(50)
                     ->get();

        return view('search', ['books' => $books]);
    }
}
