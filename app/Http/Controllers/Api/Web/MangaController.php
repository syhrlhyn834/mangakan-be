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
    $query = Manga::with('characters', 'genres', 'chapters', 'series', 'group', 'type', 'author')
        // Pencarian berdasarkan q untuk semua filter (title, genres, characters, series, author, group)
        ->when($request->q, function ($query) use ($request) {
            $search = $request->q;

            // Pencarian pada title manga
            $query->where('title', 'like', '%' . $search . '%')
                // Pencarian pada genre manga
                ->orWhereHas('genres', function ($genreQuery) use ($search) {
                    $genreQuery->where('name', 'like', '%' . $search . '%');
                })
                // Pencarian pada karakter manga
                ->orWhereHas('characters', function ($characterQuery) use ($search) {
                    $characterQuery->where('name', 'like', '%' . $search . '%');
                })
                // Pencarian pada series manga
                ->orWhereHas('series', function ($seriesQuery) use ($search) {
                    $seriesQuery->where('name', 'like', '%' . $search . '%');
                })
                // Pencarian pada author manga
                ->orWhereHas('author', function ($authorQuery) use ($search) {
                    $authorQuery->where('name', 'like', '%' . $search . '%');
                })
                // Pencarian pada group manga
                ->orWhereHas('group', function ($groupQuery) use ($search) {
                    $groupQuery->where('name', 'like', '%' . $search . '%');
                });
        })
        // Pencarian berdasarkan order (A-Z, Z-A)
        ->when($request->order, function ($query) use ($request) {
            if ($request->order == 'A-Z') {
                $query->orderBy('title', 'asc');
            } elseif ($request->order == 'Z-A') {
                $query->orderBy('title', 'desc');
            }
        })
        // Pastikan manga memiliki chapter
        ->whereHas('chapters', function ($query) {
            $query->where('id', '>', 0); // Pastikan manga memiliki chapter
        })
        // Urutkan berdasarkan yang terbaru
        ->latest()
        // Pagination, 9 manga per halaman
        ->paginate(9);

    // Mengembalikan data dengan Api Resource
    return new MangaResource(true, 'List Data Manga', $query);
}

public function filterSearch(Request $request)
{
    $query = Manga::with('characters', 'genres', 'chapters', 'series', 'group', 'type', 'author')
        // Pencarian berdasarkan q untuk semua filter
        ->when($request->q, function ($query) use ($request) {
            $search = $request->q;
            $query->where('title', 'like', '%' . $search . '%')
                  ->orWhereHas('genres', function ($genreQuery) use ($search) {
                      $genreQuery->where('name', 'like', '%' . $search . '%');
                  });
        })
        // Filter berdasarkan title
        ->when($request->title, function ($query) use ($request) {
            $query->where('title', 'like', '%' . $request->title . '%');
        })
        // Filter berdasarkan author
        ->when($request->author, function ($query) use ($request) {
            $query->whereHas('author', function ($authorQuery) use ($request) {
                $authorQuery->where('name', 'like', '%' . $request->author . '%');
            });
        })
        // Filter berdasarkan karakter
        ->when($request->character, function ($query) use ($request) {
            if ($request->character !== "") {
                $query->whereHas('characters', function ($characterQuery) use ($request) {
                    $characterQuery->where('name', 'like', '%' . $request->character . '%');
                });
            }
        })
        // Filter berdasarkan status
        ->when($request->status && $request->status !== 'All', function ($query) use ($request) {
            $query->where('status', $request->status);
        })
        // Filter berdasarkan type, hanya jika tidak "All"
        ->when($request->type && $request->type !== 'All', function ($query) use ($request) {
            $query->whereHas('type', function ($typeQuery) use ($request) {
                $typeQuery->where('name', 'like', '%' . $request->type . '%');
            });
        })
        // Filter berdasarkan genres
        // Filter berdasarkan genres dengan slug
->when($request->genres, function ($query) use ($request) {
    // Pastikan genres dikirim sebagai array slug
    $genres = is_array($request->genres)
        ? $request->genres
        : explode(',', $request->genres); // Jika diterima sebagai string, pisahkan dengan koma

    $query->whereHas('genres', function ($genreQuery) use ($genres) {
        $genreQuery->whereIn('slug', $genres); // Filter berdasarkan slug
    });
})

        // Filter berdasarkan order (A-Z, Z-A)
        ->when($request->order, function ($query) use ($request) {
            if ($request->order == 'A-Z') {
                $query->orderBy('title', 'asc');
            } elseif ($request->order == 'Z-A') {
                $query->orderBy('title', 'desc');
            }
        })
        // Pastikan manga memiliki chapter
        ->whereHas('chapters', function ($query) {
            $query->where('id', '>', 0); // Pastikan manga memiliki chapter
        })
        // Urutkan berdasarkan yang terbaru
        ->latest()
        // Pagination, 9 manga per halaman
        ->paginate(9);

    return new MangaResource(true, 'List Data Manga', $query);
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
