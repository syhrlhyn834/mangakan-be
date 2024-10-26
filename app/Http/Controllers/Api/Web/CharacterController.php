<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\CharacterResource;
use App\Models\Character;
use Illuminate\Http\Request;

class CharacterController extends Controller
{
    public function index()
    {
        $characters = Character::all();

        //return with Api Resource
        return new CharacterResource(true, 'Data Character', $characters);
    }

    /**
     * show
     *
     * @param  mixed $slug
     * @return void
     */
    public function show($slug)
    {
        $author = Character::with('mangas.characters')->where('slug', $slug)->first();

        if($author) {
            //return with Api Resource
            return new CharacterResource(true, 'List Data Mangas By Character', $author);
        }

        //return with Api Resource
        return new CharacterResource(false, 'Data Character Tidak Ditemukan!', null);
    }
}

