<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\MangaResource;
use App\Models\Manga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MangaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $mangas = Manga::with('characters', 'genres', 'chapters', 'series','group', 'type', 'author')->when(request()->q, function($mangas) {
            $mangas = $mangas->where('title', 'like', '%'. request()->q . '%');
        })->latest()->paginate(5);

        //return with Api Resource
        return new MangaResource(true, 'List Data mangas', $mangas);
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
            'image'         => 'required|image|mimes:jpeg,jpg,png|max:2000',
            'title'         => 'required|unique:mangas',
            'description'   => 'required',
            'type_id'   => 'required',
            'series_id'   => 'required',
            'author_id'   => 'required',
            'group_id'   => 'required',
            'status' => 'required|in:Published,Finished'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/mangas', $image->hashName());

        $manga = manga::create([
            'image'       => $image->hashName(),
            'title'       => $request->title,
            'slug'        => Str::slug($request->title, '-'),
            'description'     => $request->description,
            'type_id' => $request->type_id,
            'series_id' => $request->series_id,
            'author_id' => $request->author_id,
            'group_id' => $request->group_id,
            'status' => $request->status,
        ]);

        //assign genres dan characters
        $manga->genres()->attach($request->genres);
        $manga->characters()->attach($request->characters);
        $manga->save();

        if($manga) {
            //return success with Api Resource
            return new MangaResource(true, 'Data manga Berhasil Disimpan!', $manga);
        }

        //return failed with Api Resource
        return new MangaResource(false, 'Data manga Gagal Disimpan!', null);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $manga = Manga::with( 'characters', 'genres', 'chapters', 'series','group', 'type', 'author')->whereId($id)->first();

        if($manga) {
            //return success with Api Resource
            return new MangaResource(true, 'Detail Data manga!', $manga);
        }

        //return failed with Api Resource
        return new MangaResource(false, 'Detail Data manga Tidak DItemukan!', null);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Manga $manga)
    {
        $validator = Validator::make($request->all(), [
            'title'         => 'required|unique:mangas,title,'.$manga->id,
            'description'   => 'required',
            'type_id'   => 'required',
            'series_id'   => 'required',
            'author_id'   => 'required',
            'group_id'   => 'required',
            'status'     => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //check image update
        if ($request->file('image')) {

            //remove old image
            Storage::disk('local')->delete('public/mangas/'.basename($manga->image));

            //upload new image
            $image = $request->file('image');
            $image->storeAs('public/mangas', $image->hashName());

            $manga->update([
            'image'       => $image->hashName(),
            'title'       => $request->title,
            'slug'        => Str::slug($request->title, '-'),
            'description'     => $request->description,
            'type_id' => $request->type_id,
            'series_id' => $request->series_id,
            'author_id' => $request->author_id,
            'group_id' => $request->group_id,
            'status' => $request->status,
            ]);

        }

        $manga->update([
            'title'       => $request->title,
            'slug'        => Str::slug($request->title, '-'),
            'description'     => $request->description,
            'type_id' => $request->type_id,
            'series_id' => $request->series_id,
            'author_id' => $request->author_id,
            'group_id' => $request->group_id,
            'status' => $request->status,
        ]);
        //assign genres dan characters
        $manga->genres()->sync($request->genres);
        $manga->characters()->sync($request->characters);
        $manga->save();

        if($manga) {
            //return success with Api Resource
            return new MangaResource(true, 'Data manga Berhasil Diupdate!', $manga);
        }

        //return failed with Api Resource
        return new MangaResource(false, 'Data manga Gagal Disupdate!', null);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Manga $manga)
    {
         $manga->genres()->detach();
         $manga->characters()->detach();
        //remove image
        Storage::disk('local')->delete('public/mangas/'.basename($manga->image));

        if($manga->delete()) {
            //return success with Api Resource
            return new MangaResource(true, 'Data manga Berhasil Dihapus!', null);
        }

        //return failed with Api Resource
        return new MangaResource(false, 'Data manga Gagal Dihapus!', null);
    }
}
