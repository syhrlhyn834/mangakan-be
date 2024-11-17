<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\CharacterResource;
use App\Models\Character;
use Illuminate\Http\Request;

class CharacterController extends Controller
{
    public function index()
    {
        $characters = Character::with('mangas')->get();

        //return with Api Resource
        return new CharacterResource(true, 'Data Character', $characters);
    }

    /**
     * show
     *
     * @param  mixed $slug
     * @return void
     */
    public function show($slug)
    {
        $genre = Character::where('slug', $slug)->first();

        if ($genre) {
            // Mengambil mangas dengan paginate dan memuat relasi
            $mangas = $genre->mangas()
                            ->with('type', 'chapters', 'genres') // Memuat relasi yang diperlukan
                            ->paginate(9);  // Misalnya 10 manga per halaman

            // Mengupdate genre dengan mangas yang sudah dipaginate
            $genre->setRelation('mangas', $mangas);

            // Mengembalikan data menggunakan Api Resource
            return new CharacterResource(true, 'List Data Manga By Character', $genre);
        }

        // Jika genre tidak ditemukan, mengembalikan pesan error
        return new CharacterResource(false, 'Data Character Tidak Ditemukan!', null);
    }
}

