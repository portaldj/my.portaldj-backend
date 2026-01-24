<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = ['title', 'description', 'thumbnail_path', 'is_active', 'is_pro'];

    public function chapters()
    {
        return $this->hasMany(Chapter::class)->orderBy('order');
    }

    public function getThumbnailUrlAttribute()
    {
        return $this->thumbnail_path
            ? '/storage/' . $this->thumbnail_path
            : null;
    }

    public function getThumbUrlAttribute()
    {
        if (!$this->thumbnail_path)
            return null;

        // Logic to handle if stored path is "optimized" or "original"
        // We will standardize on storing ORIGINAL.
        // But if we encounter a file like "hash_optimized.jpg" (legacy from 2 mins ago),
        // pathinfo works: filename="hash_optimized".
        // variant path becomes "hash_optimized_thumb.jpg". This is WRONG.
        // We need to strip known suffixes if we want robust handling, or just fix the Controller.
        // Fixing the Controller is better. Accessor assumes stored path is base/original.

        $info = pathinfo($this->thumbnail_path);
        $path = $info['dirname'] . '/' . $info['filename'] . '_thumb.' . $info['extension'];

        return \Illuminate\Support\Facades\Storage::disk('public')->exists($path)
            ? '/storage/' . $path
            : $this->thumbnail_url;
    }

    public function getOptimizedUrlAttribute()
    {
        if (!$this->thumbnail_path)
            return null;

        $info = pathinfo($this->thumbnail_path);
        $path = $info['dirname'] . '/' . $info['filename'] . '_optimized.' . $info['extension'];

        return \Illuminate\Support\Facades\Storage::disk('public')->exists($path)
            ? '/storage/' . $path
            : $this->thumbnail_url;
    }

    protected $appends = ['thumbnail_url', 'thumb_url', 'optimized_url'];
}
