<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\GroupResource;
use App\Models\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index()
    {
        $authors = Group::all();

        //return with Api Resource
        return new GroupResource(true, 'Data Group', $authors);
    }

    /**
     * show
     *
     * @param  mixed $slug
     * @return void
     */
    public function show($slug)
    {
        $group = Group::with('mangas.groups')->where('slug', $slug)->first();

        if($group) {
            //return with Api Resource
            return new GroupResource(true, 'List Data Manga By Group', $group);
        }

        //return with Api Resource
        return new GroupResource(false, 'Data Group Tidak Ditemukan!', null);
    }
}

