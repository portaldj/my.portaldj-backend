<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingPromotion extends Model
{
    protected $fillable = [
        'event_id',
        'status',
        'press_release',
        'blog_url',
        'rejection_reason',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
