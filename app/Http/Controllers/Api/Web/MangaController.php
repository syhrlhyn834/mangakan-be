<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\MangaResource;
use App\Models\Manga;
use Illuminate\Http\Request;

class MangaController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        $mangas = Manga::with('characters', 'genres', 'chapters', 'series','group', 'type', 'author')->when(request()->q, function($mangas) {
            $mangas = $mangas->where('title', 'like', '%'. request()->q . '%');
        })->latest()->paginate(9);

        //return with Api Resource
        return new MangaResource(true, 'List Data Manga', $mangas);
    }
    /**
     * show
     *
     * @param  mixed $slug
     * @return void
     */
    public function show($slug)
    {
        $manga = Manga::with('characters', 'genres', 'chapters', 'series','group', 'type', 'author' )->where('slug', $slug)->first();

        if($manga) {
            //return with Api Resource
            return new MangaResource(true, 'Detail Data Manga', $manga);
        }

        //return with Api Resource
        return new MangaResource(true, 'Detail Data Manga Tidak Ditemukan!', null);

    }
}
