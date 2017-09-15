<?php

namespace App\Token;

use JWTAuth;

class UserFromToken
{
    public static function get()
    {
        $token = JWTAuth::getToken();
        $user  = JWTAuth::toUser($token);

        return $user;
    }
}
