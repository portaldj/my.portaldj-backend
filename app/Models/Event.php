<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'start',
        'end',
        'url',
        'is_public',
    ];

    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime',
        'is_public' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookingPromotion()
    {
        return $this->hasOne(BookingPromotion::class);
    }
}
