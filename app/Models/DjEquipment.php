<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Brand;
use App\Models\EquipmentType;

class DjEquipment extends Model
{
    protected $table = 'dj_equipments';

    protected $fillable = [
        'user_id',
        'equipment_model_id',
        'model_name', // Legacy/Fallback
        'is_public',
        'brand_id', // Legacy/Fallback
        'equipment_type_id', // Legacy/Fallback
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function equipmentModel()
    {
        return $this->belongsTo(EquipmentModel::class);
    }

    // Fallback or Direct relations
    public function brand()
    {
        // If we have direct brand_id, use it. Else access via model.
        // For simplicity during refactor, we keep definitions but might need custom attribute if we fully switch.
        // But Relation method must return Relation object.
        return $this->belongsTo(Brand::class);
    }

    public function type()
    {
        return $this->belongsTo(EquipmentType::class, 'equipment_type_id');
    }
}
