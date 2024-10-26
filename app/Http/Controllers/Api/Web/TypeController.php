<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\TypeResource;
use App\Models\Type;
use Illuminate\Http\Request;

class TypeController extends Controller
{
    public function index()
    {
        $type = Type::all();

        //return with Api Resource
        return new TypeResource(true, 'Data type', $type);
    }

    /**
     * show
     *
     * @param  mixed $slug
     * @return void
     */
    public function show($slug)
{
    $type = Type::with('mangas')->where('slug', $slug)->first();

    if ($type) {
        return new TypeResource(true, 'List Data Manga By Type', $type);
    }

    return new TypeResource(false, 'Data Type Tidak Ditemukan!', null);
}

}
