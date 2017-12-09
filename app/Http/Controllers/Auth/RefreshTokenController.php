<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

class RefreshTokenController extends Controller
{
    public function index()
    {
        header('Access-Control-Expose-Headers: Authorization');

        return response()->json([], 200);
    }
}
