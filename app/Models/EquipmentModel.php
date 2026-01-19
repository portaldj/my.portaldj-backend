<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquipmentModel extends Model
{
    protected $fillable = ['brand_id', 'equipment_type_id', 'name', 'slug', 'is_verified'];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function type()
    {
        return $this->belongsTo(EquipmentType::class, 'equipment_type_id');
    }

    public function djEquipments()
    {
        return $this->hasMany(DjEquipment::class);
    }
}
