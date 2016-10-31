<?php

namespace App\Http\Controllers;

use Auth;
use App\Bookshelf;
use Illuminate\Http\Request;

class UserController extends Controller
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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        $my_books = Bookshelf::with('book')->where('user_id', Auth::user()->id)->get();
        return view('home', ['my_books' => $my_books]);
    }
}
