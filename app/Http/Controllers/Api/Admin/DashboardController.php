<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Chapter;
use App\Models\Character;
use App\Models\Genre;
use App\Models\Group;
use App\Models\Manga;
use App\Models\Series;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
  /**
     * index
     *
     * @return void
     */
    public function index()
    {
        $authors = Author::count();
        $chapters   = Chapter::count();
        $characters = Character::count();
        $genres      = Genre::count();
        $groups      = Group::count();
        $mangas      = Manga::count();
        $series      = Series::count();


        return response()->json([
            'success' => true,
            'message' => 'List Count Data Table',
            'data' => [
                'authors'      => $authors,
                'chapters'   => $chapters,
                'characters' => $characters,
                'genres'      => $genres,
                'groups'      => $groups,
                'mangas'      => $mangas,
                'series'      => $series,
            ],
        ], 200);
    }
}
