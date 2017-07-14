<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\SearchRequest;
use App\Book;

class SearchController extends Controller
{
    public function search(SearchRequest $request)
    {
        $title = $request->title;
        $books = Book::select('books.id', 'title', 'title_slug', 'bookshelves.user_id')
                     ->where('title', 'LIKE', "%{$title}%")
                     ->leftJoin('bookshelves', 'books.id', '=', 'bookshelves.book_id')
                     ->limit(50)
                     ->get();

        return response()->json($books);
    }
}
