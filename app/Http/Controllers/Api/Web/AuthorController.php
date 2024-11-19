<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuthorResource;
use App\Models\Author;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function index()
    {
        $characters = Author::with('mangas')->get();

        //return with Api Resource
        return new AuthorResource(true, 'Data Author', $characters);
    }

    /**
     * show
     *
     * @param  mixed $slug
     * @return void
     */
    public function show($slug)
    {
        $genre = Author::where('slug', $slug)->first();

        if ($genre) {
            // Mengambil mangas dengan paginate dan memuat relasi
            $mangas = $genre->mangas()
                            ->with('type', 'chapters', 'genres') // Memuat relasi yang diperlukan
                            ->paginate(18);  // Misalnya 10 manga per halaman

            // Mengupdate genre dengan mangas yang sudah dipaginate
            $genre->setRelation('mangas', $mangas);

            // Mengembalikan data menggunakan Api Resource
            return new AuthorResource(true, 'List Data Manga By Author', $genre);
        }

        // Jika genre tidak ditemukan, mengembalikan pesan error
        return new AuthorResource(false, 'Data Authpr Tidak Ditemukan!', null);
    }
}
