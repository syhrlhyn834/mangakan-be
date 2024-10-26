<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Header extends Model
{
    use HasFactory;
    protected $fillable = [
        'title', 'name', 'image', 'image2'
    ];
/**
     * image
     *
     * @return Attribute
     */
    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => url('/storage/headers/' . $value),
        );
    }

    protected function image2(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => url('/storage/headers/' . $value),
        );
    }
}
