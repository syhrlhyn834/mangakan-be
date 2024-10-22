<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    protected $fillable = [
        'name', 'slug'
    ];

    public function mangas()
    {
        return $this->hasMany(Manga::class);
    }
}
