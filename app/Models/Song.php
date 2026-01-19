<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    protected $fillable = [
        'artist_name',
        'track_name',
        'bpm',
        'key',
        'remix_type',
        'genre_id',
        'file_path',
        'is_pro_only'
    ];

    public function genre()
    {
        return $this->belongsTo(MusicGenre::class, 'genre_id');
    }

    public function downloads()
    {
        return $this->hasMany(Download::class);
    }

}
