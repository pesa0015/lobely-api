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

    public static function getTransformer()
    {
        return \App\Http\Transformer\UserTransformer::class;
    }

    public static function getIncludes()
    {
        return ['like'];
    }

    public static function getUser($request)
    {
        return $request->attributes->user;
    }

    public function getFirstName()
    {
        return explode(' ', $this->name)[0];
    }

    public function books()
    {
        return $this->belongsToMany('App\Book', 'bookshelves', 'user_id', 'book_id')
            ->withPivot('comment')
            ->withTimestamps();
    }

    public function hearts()
    {
        return $this->hasMany('App\Heart');
    }

    public static function generateSlug($slug)
    {
        $unique = false;

        while (!$unique) {
            $newSlug = str_slug($slug) . '-' . str_random(8);

            $user = self::where('slug', $newSlug)->first();

            if (!$user) {
                $unique = true;
            }
        }

        return $newSlug;
    }
}
