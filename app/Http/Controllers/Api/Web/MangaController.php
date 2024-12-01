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
    $query = Manga::with('characters', 'genres', 'chapters', 'series', 'group', 'author')
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
        // Pagination, 18 manga per halaman
        ->paginate(18);

    // Mengembalikan data dengan Api Resource
    return new MangaResource(true, 'List Data Manga', $query);
}

public function filterSearch(Request $request)
{
    $query = Manga::with('characters', 'genres', 'chapters', 'series', 'group', 'author')
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

        // Filter berdasarkan order (A-Z, Z-A, Latest Update, Latest Added)
->when($request->order, function ($query) use ($request) {
    if ($request->order == 'A-Z') {
        $query->orderBy('title', 'asc'); // Order by title alphabetically in ascending order
    } elseif ($request->order == 'Z-A') {
        $query->orderBy('title', 'desc'); // Order by title alphabetically in descending order
    } elseif ($request->order == 'Latest Update') {
        $query->where('updated_at', '>=', now()->subHours(10)); // Filter by last 10 hours of updates
    } elseif ($request->order == 'Latest Added') {
        $query->where('created_at', '>=', now()->subHours(10)); // Filter by last 10 hours of additions
    }
})

        // Pastikan manga memiliki chapter
        ->whereHas('chapters', function ($query) {
            $query->where('id', '>', 0); // Pastikan manga memiliki chapter
        })
        // Urutkan berdasarkan yang terbaru
        ->latest()
        // Pagination, 18 manga per halaman
        ->paginate(18);

    return new MangaResource(true, 'List Data Manga', $query);
}

public function mangaPublishing()
{
    $mangas = Manga::with('characters', 'genres', 'chapters', 'series', 'group', 'author')
        ->when(request()->q, function($query) {
            $query->where('title', 'like', '%' . request()->q . '%');
        })
        // Menambahkan filter untuk memastikan manga memiliki minimal 1 chapter
        ->whereHas('chapters', function($query) {
            $query->where('id', '>', 0); // Pastikan manga memiliki chapter
        })
        ->where('status', 'publishing') // Filter untuk status 'publishing'
        ->latest()
        ->paginate(18);

    //return with Api Resource
    return new MangaResource(true, 'List Data Manga', $mangas);
}


public function mangaFinished()
{
    $mangas = Manga::with('characters', 'genres', 'chapters', 'series', 'group', 'author')
        ->when(request()->q, function($query) {
            $query->where('title', 'like', '%' . request()->q . '%');
        })
        // Menambahkan filter untuk memastikan manga memiliki minimal 1 chapter
        ->whereHas('chapters', function($query) {
            $query->where('id', '>', 0); // Pastikan manga memiliki chapter
        })
        ->where('status', 'finished') // Filter untuk status 'finished'
        ->latest()
        ->paginate(18);

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
        $manga = Manga::with('characters', 'genres', 'chapters', 'series','group','author' )->where('slug', $slug)->first();

        if($manga) {
            //return with Api Resource
            return new MangaResource(true, 'Detail Data Manga', $manga);
        }

        //return with Api Resource
        return new MangaResource(true, 'Detail Data Manga Tidak Ditemukan!', null);

    }
}
