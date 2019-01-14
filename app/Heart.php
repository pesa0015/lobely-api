<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Heart extends Model
{
    const STATUS_PENDING  = 0;
    const STATUS_APPROVED = 1;
    const STATUS_DENIED   = 2;

    protected $fillable = [
        'user_id', 'heart_user_id', 'book_id', 'status', 'have_read'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('heart_user_id', $userId)->orWHere('user_id', $userId);
    }
}
