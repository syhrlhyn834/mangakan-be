<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuthorResource;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //get authors
        $authors = Author::with('mangas')->when(request()->q, function($authors) {
            $authors = $authors->where('name', 'like', '%'. request()->q . '%');
        })->latest()->paginate(5);

        //return with Api Resource
        return new AuthorResource(true, 'List Data authors', $authors);
    }

    public function authorView()
    {
        // Get genres with optional search query
        $genres = Author::when(request()->q, function($query) {
            return $query->where('name', 'like', '%'. request()->q . '%');
        })
        ->latest()  // Sort by the latest
        ->get();  // Get the results

        // Return response with API Resource
        return new AuthorResource(true, 'List Data authors', $genres);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|unique:authors',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //create Author
        $author = Author::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
        ]);

        if($author) {
            //return success with Api Resource
            return new AuthorResource(true, 'Data author Berhasil Disimpan!', $author);
        }

        //return failed with Api Resource
        return new AuthorResource(false, 'Data author Gagal Disimpan!', null);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $author = Author::whereId($id)->first();

        if($author) {
            //return success with Api Resource
            return new AuthorResource(true, 'Detail Data author!', $author);
        }

        //return failed with Api Resource
        return new AuthorResource(false, 'Detail Data author Tidak DItemukan!', null);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Author $author)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|unique:authors,name,'.$author->id,
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //update author
        $author->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
        ]);

        if($author) {
            //return success with Api Resource
            return new AuthorResource(true, 'Data author Berhasil Diupdate!', $author);
        }

        //return failed with Api Resource
        return new AuthorResource(false, 'Data author Gagal Diupdate!', null);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Author $author)
    {
        if($author->delete()) {
            //return success with Api Resource
            return new AuthorResource(true, 'Data author Berhasil Dihapus!', null);
        }

        //return failed with Api Resource
        return new AuthorResource(false, 'Data author Gagal Dihapus!', null);
    }
}


