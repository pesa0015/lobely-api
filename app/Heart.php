<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Constants\HeartStatus;

class Heart extends Model
{
    protected $fillable = [
        'user_id', 'heart_user_id', 'book_id', 'status', 'have_read'
    ];

    public function getCreatedAtAttribute($date)
    {
        return \Carbon\Carbon::parse($date)->format('Y-m-d H:i:s');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
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
