<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'user_id',
        'content',
        'image_path',
        'location_tag_type',
        'location_tag_id',
    ];

    public function locationTag()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function taggedClubs()
    {
        return $this->morphedByMany(Club::class, 'taggable', 'post_tags')->withTimestamps();
    }

    public function taggedCities()
    {
        return $this->morphedByMany(City::class, 'taggable', 'post_tags')->withTimestamps();
    }

    public function taggedDjs()
    {
        return $this->morphedByMany(User::class, 'taggable', 'post_tags')->withTimestamps();
    }

    // Helper to get formatted tags for API/Frontend
    public function getTagsAttribute()
    {
        $tags = collect();

        if ($this->relationLoaded('taggedClubs')) {
            foreach ($this->taggedClubs as $club) {
                $tags->push(['id' => $club->id, 'name' => $club->name, 'type' => 'club']);
            }
        }

        if ($this->relationLoaded('taggedCities')) {
            foreach ($this->taggedCities as $city) {
                $tags->push(['id' => $city->id, 'name' => $city->name, 'type' => 'city']);
            }
        }

        if ($this->relationLoaded('taggedDjs')) {
            foreach ($this->taggedDjs as $dj) {
                // Ensure profile is loaded to avoid N+1 if not eager loaded, though we fixed Service
                $username = $dj->profile ? $dj->profile->username : null;
                $tags->push(['id' => $dj->id, 'name' => $dj->name, 'type' => 'user', 'username' => $username]);
            }
        }

        return $tags;
    }

    public function getImageUrlAttribute()
    {
        return $this->image_path
            ? '/storage/' . $this->image_path
            : null;
    }

    public function getOptimizedUrlAttribute()
    {
        if (!$this->image_path)
            return null;

        $info = pathinfo($this->image_path);
        $path = $info['dirname'] . '/' . $info['filename'] . '_optimized.' . $info['extension'];

        return \Illuminate\Support\Facades\Storage::disk('public')->exists($path)
            ? '/storage/' . $path
            : $this->image_url;
    }

    protected $appends = ['tags', 'image_url', 'optimized_url'];
}
