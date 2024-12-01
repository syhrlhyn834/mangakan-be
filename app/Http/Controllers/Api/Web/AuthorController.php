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
    $authors = Author::with(['mangas' => function ($query) {
        // Pastikan manga memiliki chapter yang valid
        $query->whereHas('chapters', function ($chapterQuery) {
            $chapterQuery->where('id', '>', 0); // Pastikan manga memiliki chapter
        });
    }])->get();

    // Return with Api Resource
    return new AuthorResource(true, 'Data Author', $authors);
}

    /**
     * show
     *
     * @param  mixed $slug
     * @return void
     */
    public function show($slug)
{
    $author = Author::where('slug', $slug)->first();

    if ($author) {
        // Memuat manga dengan filter chapter yang lebih dari 0
        $mangas = $author->mangas()
                         ->with('chapters', 'genres') // Memuat relasi yang diperlukan
                         ->whereHas('chapters', function ($query) {
                             $query->where('id', '>', 0); // Pastikan manga memiliki chapter
                         })
                         ->paginate(18); // Misalnya 18 manga per halaman

        // Mengupdate author dengan mangas yang sudah dipaginate
        $author->setRelation('mangas', $mangas);

        // Mengembalikan data menggunakan Api Resource
        return new AuthorResource(true, 'List Data Manga By Author', $author);
    }

    // Jika author tidak ditemukan, mengembalikan pesan error
    return new AuthorResource(false, 'Data Author Tidak Ditemukan!', null);
}

}
