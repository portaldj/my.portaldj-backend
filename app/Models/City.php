<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    /**
     * Una ciudad pertenece a un país.
     * @return BelongsTo
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Una ciudad puede tener muchos usuarios.
     * @return HasMany
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Una ciudad puede tener muchos clubs.
     * @return HasMany
     */
    public function clubs(): HasMany
    {
        return $this->hasMany(Club::class);
    }
}
