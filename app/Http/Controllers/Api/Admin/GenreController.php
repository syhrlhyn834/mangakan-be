<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\GenreResource;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class GenreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //get genres
        $genres = Genre::when(request()->q, function($genres) {
            $genres = $genres->where('name', 'like', '%'. request()->q . '%');
        })->latest()->paginate(5);

        //return with Api Resource
        return new GenreResource(true, 'List Data genres', $genres);
    }

    public function genresView()
    {
        // Get genres with optional search query
        $genres = Genre::when(request()->q, function($query) {
            return $query->where('name', 'like', '%'. request()->q . '%');
        })
        ->latest()  // Sort by the latest
        ->get();  // Get the results

        // Return response with API Resource
        return new GenreResource(true, 'List Data genres', $genres);
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
            'name'     => 'required|unique:genres',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //create genre
        $genre = genre::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
        ]);

        if($genre) {
            //return success with Api Resource
            return new GenreResource(true, 'Data genre Berhasil Disimpan!', $genre);
        }

        //return failed with Api Resource
        return new GenreResource(false, 'Data genre Gagal Disimpan!', null);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $genre = Genre::whereId($id)->first();

        if($genre) {
            //return success with Api Resource
            return new GenreResource(true, 'Detail Data genre!', $genre);
        }

        //return failed with Api Resource
        return new GenreResource(false, 'Detail Data genre Tidak DItemukan!', null);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Genre $genre)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|unique:genres,name,'.$genre->id,
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //update genre
        $genre->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
        ]);

        if($genre) {
            //return success with Api Resource
            return new GenreResource(true, 'Data genre Berhasil Diupdate!', $genre);
        }

        //return failed with Api Resource
        return new GenreResource(false, 'Data genre Gagal Diupdate!', null);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Genre $genre)
    {
        if($genre->delete()) {
            //return success with Api Resource
            return new GenreResource(true, 'Data genre Berhasil Dihapus!', null);
        }

        //return failed with Api Resource
        return new GenreResource(false, 'Data genre Gagal Dihapus!', null);
    }
}
