<?php

namespace App;

use App\Constants\HeartStatus;

class Heart extends BaseModel
{
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

    public function messages()
    {
        return $this->hasMany('App\Message');
    }

    public function denied()
    {
        $this->update([
            'status'    => HeartStatus::DENIED,
            'have_read' => true,
        ]);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('heart_user_id', $userId)->orWHere('user_id', $userId)->first();
    }
}
