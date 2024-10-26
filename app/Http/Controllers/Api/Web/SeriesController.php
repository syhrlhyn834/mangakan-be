<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\SeriesResource;
use App\Models\Series;
use Illuminate\Http\Request;

class SeriesController extends Controller
{
    public function index()
    {
        $series = Series::all();

        //return with Api Resource
        return new SeriesResource(true, 'Data Series', $series);
    }

    /**
     * show
     *
     * @param  mixed $slug
     * @return void
     */
    public function show($slug)
    {
        $series = Series::with('mangas.series')->where('slug', $slug)->first();

        if($series) {
            //return with Api Resource
            return new SeriesResource(true, 'List Data Manga By Series', $series);
        }

        //return with Api Resource
        return new SeriesResource(false, 'Data Series Tidak Ditemukan!', null);
    }
}
