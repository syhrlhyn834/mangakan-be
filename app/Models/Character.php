<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Character extends Model
{
    protected $fillable = [
        'name', 'slug'
    ];

    public function mangas()
    {
        return $this->belongsToMany(Manga::class, 'manga_character', 'character_id', 'manga_id');
    }
}
