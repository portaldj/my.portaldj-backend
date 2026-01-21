<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    protected $fillable = ['course_id', 'title', 'order', 'content', 'video_url'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function exam()
    {
        return $this->hasOne(Exam::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'chapter_user')
            ->withPivot('completed_at')
            ->withTimestamps();
    }
}
