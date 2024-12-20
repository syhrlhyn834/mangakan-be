<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChapterResource;
use App\Models\Chapter;
use Illuminate\Http\Request;

class ChapterController extends Controller
{
    public function index()
    {
        $chapters = Chapter::all();

        //return with Api Resource
        return new ChapterResource(true, 'Data Chapter', $chapters);
    }

    public function show($slug)
    {
        $chapter = Chapter::with('manga.chapters')->where('slug', $slug)->first();

        if($chapter) {
            //return with Api Resource
            return new ChapterResource(true, 'List Data Mangas By Chapter', $chapter);
        }

        //return with Api Resource
        return new ChapterResource(false, 'Data Character Tidak Ditemukan!', null);
    }
}

