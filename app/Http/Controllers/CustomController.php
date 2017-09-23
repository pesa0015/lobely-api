<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Transformer\Transform;
use JWTAuth;

class CustomController extends Controller
{
    protected $user;
    protected $transform;

    public function __construct(Request $request)
    {
        $header = $request->header('Authorization');
        $token = str_replace('Bearer ', '', $header);
        
        $user  = JWTAuth::toUser($token);
        $this->user = $user;

        $this->transform = new Transform();
    }
}
