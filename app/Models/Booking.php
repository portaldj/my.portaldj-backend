<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    public function guest(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guest_user_id');
    }
    
    public function dj(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dj_user_id');
    }
}
