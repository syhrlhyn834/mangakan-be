<?php

namespace App\Models;

use App\Models\Chapter;
use App\Models\Character;
use App\Models\Genre;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Manga extends Model
{
    protected $fillable = [
        'title', 'slug', 'description', 'series_id', 'author_id', 'group_id', 'status', 'image'
    ];

    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'manga_genre', 'manga_id', 'genre_id');
    }

    // Relasi dengan character menggunakan tabel pivot manga_character
    public function characters()
    {
        return $this->belongsToMany(Character::class, 'manga_character', 'manga_id', 'character_id');
    }

    public function chapters()
    {
        return $this->hasMany(Chapter::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function series()
    {
        return $this->belongsTo(Series::class);
    }

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => url('/storage/mangas/' . $value),
        );
    }

    /**
     * createdAt
     *
     * @return Attribute
     */
    protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Carbon::parse($value)
                ->setTimezone('Asia/Jakarta') // Set waktu ke zona Jakarta (WIB)
                ->locale('id') // Set lokal ke Indonesia
                ->translatedFormat('l, d F Y, H:i T') // Format sesuai kebutuhan
        );
    }



    /**
     * updatedAt
     *
     * @return Attribute
     */
    protected function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Carbon::parse($value)
                ->setTimezone('Asia/Jakarta') // Set waktu ke zona Jakarta (WIB)
                ->locale('id') // Set lokal ke Indonesia
                ->translatedFormat('l, d F Y, H:i T') // Format sesuai kebutuhan
        );
    }
}
