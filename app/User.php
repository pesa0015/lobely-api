<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'facebook_id', 'name', 'email', 'gender', 'interested_in_gender', 'password', 'birth_date', 'bio'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public static function getUser($request)
    {
        return $request->attributes->user;
    }

    public function books()
    {
        return $this->belongsToMany('App\Book', 'bookshelves', 'user_id', 'book_id');
    }
}
