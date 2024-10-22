<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    protected $fillable = [
        'name', 'slug'
    ];

    public function mangas()
    {
        return $this->belongsToMany(Manga::class, 'manga_genre', 'genre_id', 'manga_id');
    }
}
