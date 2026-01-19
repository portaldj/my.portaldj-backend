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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
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
                $tags->push(['id' => $dj->id, 'name' => $dj->name, 'type' => 'user']);
            }
        }

        return $tags;
    }

    protected $appends = ['tags'];
}
