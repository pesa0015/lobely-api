<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bookshelf extends Model
{
	public $timestamps = false;

    protected $fillable = [
        'user_id', 'book_id', 'comment'
    ];

    public function user()
    {
    	return $this->belongsTo('App\User', 'user_id');
    }

    public function book()
    {
    	return $this->belongsTo('App\Book', 'book_id');
    }
}
