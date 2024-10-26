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
        $authors = Author::all();

        //return with Api Resource
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
        $author = Author::with('mangas.authors')->where('slug', $slug)->first();

        if($author) {
            //return with Api Resource
            return new AuthorResource(true, 'List Data Manga By Author', $author);
        }

        //return with Api Resource
        return new AuthorResource(false, 'Data Author Tidak Ditemukan!', null);
    }
}
