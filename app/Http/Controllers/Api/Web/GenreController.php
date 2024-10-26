<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\GenreResource;
use App\Models\Genre;
use Illuminate\Http\Request;

class GenreController extends Controller
{
    public function index()
    {
        $genres = Genre::all();

        //return with Api Resource
        return new GenreResource(true, 'Data Genre', $genres);
    }

    /**
     * show
     *
     * @param  mixed $slug
     * @return void
     */
    public function show($slug)
    {
        $genre = Genre::with('mangas.genres')->where('slug', $slug)->first();

        if($genre) {
            //return with Api Resource
            return new GenreResource(true, 'List Data Manga By Genre', $genre);
        }

        //return with Api Resource
        return new GenreResource(false, 'Data Genre Tidak Ditemukan!', null);
    }
}
