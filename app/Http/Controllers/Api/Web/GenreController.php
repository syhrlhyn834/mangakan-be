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
        $genres = Genre::with('mangas')->get();

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
    $genre = Genre::where('slug', $slug)->first();

    if ($genre) {
        // Mengambil mangas dengan paginate dan memuat relasi
        $mangas = $genre->mangas()
                        ->with('type', 'chapters', 'genres') // Memuat relasi yang diperlukan
                        ->paginate(18);  // Misalnya 10 manga per halaman

        // Mengupdate genre dengan mangas yang sudah dipaginate
        $genre->setRelation('mangas', $mangas);

        // Mengembalikan data menggunakan Api Resource
        return new GenreResource(true, 'List Data Manga By Genre', $genre);
    }

    // Jika genre tidak ditemukan, mengembalikan pesan error
    return new GenreResource(false, 'Data Genre Tidak Ditemukan!', null);
}

}
