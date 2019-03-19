<?php

namespace App;

class Message extends BaseModel
{
    protected $fillable = [
        'heart_id',
        'user_id',
        'body',
        'have_read',
    ];

    public function heart()
    {
        return $this->belongsTo('App\Heart');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
