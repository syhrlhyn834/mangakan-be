<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\GroupResource;
use App\Models\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index()
    {
        $authors = Group::with('mangas')->get();

        //return with Api Resource
        return new GroupResource(true, 'Data Group', $authors);
    }

    public function show($slug)
{
    $genre = Group::where('slug', $slug)->first();

    if ($genre) {
        // Mengambil mangas dengan paginate dan memuat relasi
        $mangas = $genre->mangas()
                        ->with('type', 'chapters', 'genres') // Memuat relasi yang diperlukan
                        ->paginate(18);  // Misalnya 10 manga per halaman

        // Mengupdate genre dengan mangas yang sudah dipaginate
        $genre->setRelation('mangas', $mangas);

        // Mengembalikan data menggunakan Api Resource
        return new GroupResource(true, 'List Data Manga By Group', $genre);
    }

    // Jika genre tidak ditemukan, mengembalikan pesan error
    return new GroupResource(false, 'Data Group Tidak Ditemukan!', null);
}
}

