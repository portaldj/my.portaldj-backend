<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquipmentType extends Model
{
    protected $fillable = ['name', 'slug'];

    public function models()
    {
        return $this->hasMany(EquipmentModel::class);
    }
}
