<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EquipmentType extends Model
{
    /**
     * Un tipo de equipamiento puede tener muchas marcas.
     * @return HasMany
     */
    public function brands(): HasMany
    {
        return $this->hasMany(EquipmentBrand::class);
    }
}
