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
        'preview_file_path',
        'download_url',
        'is_pro_only',
        'visible_from',
        'visible_until',
        'download_limit',
    ];

    protected function casts(): array
    {
        return [
            'visible_from' => 'datetime',
            'visible_until' => 'datetime',
        ];
    }

    public function genre()
    {
        return $this->belongsTo(MusicGenre::class, 'genre_id');
    }

    public function downloads()
    {
        return $this->hasMany(Download::class);
    }

}
