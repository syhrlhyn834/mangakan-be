<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\MangaResource;
use App\Models\Manga;
use Illuminate\Http\Request;

class MangaController extends Controller
{
    public function index(Request $request)
    {
        $mangas = Manga::with('characters', 'genres', 'chapters', 'series','group', 'type', 'author')
            ->when($request->q, function($query) use ($request) {
                $query->where('title', 'like', '%' . $request->q . '%');
            })
            // Filter berdasarkan genre
            ->when($request->genres, function($query) use ($request) {
                $query->whereHas('genres', function($genreQuery) use ($request) {
                    $genreQuery->whereIn('name', $request->genres);
                });
            })
            // Filter berdasarkan author
            ->when($request->author, function($query) use ($request) {
                $query->whereHas('author', function($authorQuery) use ($request) {
                    $authorQuery->where('name', 'like', '%' . $request->author . '%');
                });
            })
            // Filter berdasarkan type
            ->when($request->type, function($query) use ($request) {
                $query->whereHas('type', function($typeQuery) use ($request) {
                    $typeQuery->where('name', $request->type);
                });
            })
            // Filter berdasarkan status (misalnya, 'ongoing', 'completed')
            ->when($request->status, function($query) use ($request) {
                $query->where('status', $request->status);
            })
            // Filter berdasarkan urutan (misalnya, 'A-Z', 'Z-A')
            ->when($request->order, function($query) use ($request) {
                if ($request->order == 'A-Z') {
                    $query->orderBy('title', 'asc');
                } else if ($request->order == 'Z-A') {
                    $query->orderBy('title', 'desc');
                }
            })
            // Menambahkan filter untuk memastikan manga memiliki minimal 1 chapter
            ->whereHas('chapters', function($query) {
                $query->where('id', '>', 0); // Pastikan manga memiliki chapter
            })
            ->latest()
            ->paginate(9);

        // Return dengan Api Resource
        return new MangaResource(true, 'List Data Manga', $mangas);
    }


    public function manhwaHome()
    {
        $mangas = Manga::with('characters', 'genres', 'chapters', 'series', 'group', 'type', 'author')
            ->when(request()->q, function($query) {
                $query->where('title', 'like', '%' . request()->q . '%');
            })
            ->whereHas('type', function($query) {
                $query->where('name', 'manhwa'); // Pastikan nama kolom yang sesuai di tabel tipe
            })
            // Menambahkan filter untuk memastikan manga memiliki minimal 1 chapter
            ->whereHas('chapters', function($query) {
                $query->where('id', '>', 0); // Pastikan manga memiliki chapter
            })
            ->latest()
            ->paginate(9);

        //return with Api Resource
        return new MangaResource(true, 'List Data Manhwa', $mangas);
    }

    public function doujinHome()
    {
        $mangas = Manga::with('characters', 'genres', 'chapters', 'series', 'group', 'type', 'author')
            ->when(request()->q, function($query) {
                $query->where('title', 'like', '%' . request()->q . '%');
            })
            ->whereHas('type', function($query) {
                $query->where('name', 'doujinshi'); // Pastikan nama kolom yang sesuai di tabel tipe
            })
            // Menambahkan filter untuk memastikan manga memiliki minimal 1 chapter
            ->whereHas('chapters', function($query) {
                $query->where('id', '>', 0); // Pastikan manga memiliki chapter
            })
            ->latest()
            ->paginate(9);

        //return with Api Resource
        return new MangaResource(true, 'List Data Doujin', $mangas);
    }

    public function mangaHome()
    {
        $mangas = Manga::with('characters', 'genres', 'chapters', 'series', 'group', 'type', 'author')
            ->when(request()->q, function($query) {
                $query->where('title', 'like', '%' . request()->q . '%');
            })
            ->whereHas('type', function($query) {
                $query->where('name', 'manga'); // Pastikan nama kolom yang sesuai di tabel tipe
            })
            // Menambahkan filter untuk memastikan manga memiliki minimal 1 chapter
            ->whereHas('chapters', function($query) {
                $query->where('id', '>', 0); // Pastikan manga memiliki chapter
            })
            ->latest()
            ->paginate(9);

        //return with Api Resource
        return new MangaResource(true, 'List Data Manga', $mangas);
    }

    public function doujinmangaHome()
    {
        $mangas = Manga::with('characters', 'genres', 'chapters', 'series', 'group', 'type', 'author')
            ->when(request()->q, function($query) {
                $query->where('title', 'like', '%' . request()->q . '%');
            })
            ->whereHas('type', function($query) {
                $query->whereIn('name', ['doujinshi', 'manga']); // Sesuaikan kolom 'name' jika berbeda
            })
            // Menambahkan filter untuk memastikan manga memiliki minimal 1 chapter
            ->whereHas('chapters', function($query) {
                $query->where('id', '>', 0); // Pastikan manga memiliki chapter
            })
            ->latest()
            ->paginate(9);

        //return with Api Resource
        return new MangaResource(true, 'List Data Manga', $mangas);
    }

    public function mangaPublishing()
    {
        $mangas = Manga::with('characters', 'genres', 'chapters', 'series', 'group', 'type', 'author')
            ->when(request()->q, function($query) {
                $query->where('title', 'like', '%' . request()->q . '%');
            })
            ->whereHas('type', function($query) {
                $query->where('status', 'Publishing'); // Pastikan nama kolom yang sesuai di tabel tipe
            })
            // Menambahkan filter untuk memastikan manga memiliki minimal 1 chapter
            ->whereHas('chapters', function($query) {
                $query->where('id', '>', 0); // Pastikan manga memiliki chapter
            })
            ->latest()
            ->paginate(9);

        //return with Api Resource
        return new MangaResource(true, 'List Data Manga', $mangas);
    }

    public function mangaFinished()
    {
        $mangas = Manga::with('characters', 'genres', 'chapters', 'series', 'group', 'type', 'author')
            ->when(request()->q, function($query) {
                $query->where('title', 'like', '%' . request()->q . '%');
            })
            ->whereHas('type', function($query) {
                $query->where('status', 'Finished'); // Pastikan nama kolom yang sesuai di tabel tipe
            })
            // Menambahkan filter untuk memastikan manga memiliki minimal 1 chapter
            ->whereHas('chapters', function($query) {
                $query->where('id', '>', 0); // Pastikan manga memiliki chapter
            })
            ->latest()
            ->paginate(9);

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
