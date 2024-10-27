<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Chapter extends Model
{
    protected $fillable = [
        'manga_id', 'title', 'slug', 'chapter_number', 'content', 'image'
    ];

    public function manga()
    {
        return $this->belongsTo(Manga::class);
    }



    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => url('/storage/chapters/' . $value),
        );
    }
    protected function content(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => filter_var($value, FILTER_VALIDATE_URL) ? $value : url('/storage/chapters_content/' . $value),
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
            get: fn ($value) => \Carbon\Carbon::parse($value)->translatedFormat('l, d F Y'),
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
            get: fn ($value) => \Carbon\Carbon::parse($value)->translatedFormat('l, d F Y'),
        );
    }

}
