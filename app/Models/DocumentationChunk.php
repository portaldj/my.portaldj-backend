<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DocumentationChunk extends Model
{
    use HasFactory;

    protected $fillable = ['equipment_model_id', 'topic', 'content', 'embedding'];

    public function equipmentModel()
    {
        return $this->belongsTo(EquipmentModel::class);
    }
}
