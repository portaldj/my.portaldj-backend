<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    /**
     * La solicitud de booking es hecha por un usuario (guest)
     * @return BelongsTo
     */
    public function guest(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guest_user_id');
    }

    /**
     * La solicitud es para un DJ específico.
     * @return BelongsTo
     */
    public function dj(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dj_user_id');
    }
}
