<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        if (\Auth::check())
            return \Redirect::to('bookshelf');
        return view('start');
    }
}
