<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'description', 'thumbnail_path', 'is_active', 'is_pro'];

    public function chapters()
    {
        return $this->hasMany(Chapter::class)->orderBy('order');
    }

    public function getThumbnailUrlAttribute()
    {
        if (!$this->thumbnail_path)
            return null;

        // Check Local
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($this->thumbnail_path)) {
            return '/storage/' . $this->thumbnail_path;
        }

        return \Illuminate\Support\Facades\Storage::disk('r2-public')->url($this->thumbnail_path);
    }

    public function getThumbUrlAttribute()
    {
        if (!$this->thumbnail_path)
            return null;

        $info = pathinfo($this->thumbnail_path);
        $path = $info['dirname'] . '/' . $info['filename'] . '_thumb.' . $info['extension'];

        // Check Local Variant
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
            return '/storage/' . $path;
        }

        // Check Local Original (Fallback for legacy)
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($this->thumbnail_path)) {
            return '/storage/' . $this->thumbnail_path;
        }

        return \Illuminate\Support\Facades\Storage::disk('r2-public')->url($path);
    }

    public function getOptimizedUrlAttribute()
    {
        if (!$this->thumbnail_path)
            return null;

        $info = pathinfo($this->thumbnail_path);
        $path = $info['dirname'] . '/' . $info['filename'] . '_optimized.' . $info['extension'];

        // Check Local Variant
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
            return '/storage/' . $path;
        }

        // Check Local Original (Fallback for legacy)
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($this->thumbnail_path)) {
            return '/storage/' . $this->thumbnail_path;
        }

        return \Illuminate\Support\Facades\Storage::disk('r2-public')->url($path);
    }

    protected $appends = ['thumbnail_url', 'thumb_url', 'optimized_url'];
}
