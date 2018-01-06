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

    public function show(Request $request, $slug)
    {
        $bookRaw = Book::where('slug', $slug)->firstOrFail();

        $onMyBookshelf = $bookRaw->users()->where('user_id', $this->user->id)->exists();

        if ($request->has('showUsers') && $onMyBookshelf) {
            $usersRaw = $bookRaw->users()->where('user_id', '!=', $this->user->id)->get();

            $this->transform->setBook($bookRaw);
            $users = $this->transform->collection($usersRaw, User::getTransformer(), User::getIncludes());

            return response()->json($users);
        }

        if ($onMyBookshelf) {
            $transformer = Book::getBookshelfTransformer();
        } else {
            $transformer = Book::getTransformer();
        }

        $book = $this->transform->item($bookRaw, $transformer, Book::getIncludes());

        return response()->json($book);
    }
}
