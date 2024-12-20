<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\SeriesResource;
use App\Models\Series;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SeriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //get series
        $series = Series::when(request()->q, function($series) {
            $series = $series->where('name', 'like', '%'. request()->q . '%');
        })->latest()->paginate(5);

        //return with Api Resource
        return new SeriesResource(true, 'List Data series', $series);
    }

    public function seriesView()
    {
        // Get genres with optional search query
        $genres = Series::when(request()->q, function($query) {
            return $query->where('name', 'like', '%'. request()->q . '%');
        })
        ->latest()  // Sort by the latest
        ->get();  // Get the results

        // Return response with API Resource
        return new SeriesResource(true, 'List Data series', $genres);
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
            'name'     => 'required|unique:series',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //create series
        $series = Series::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
        ]);

        if($series) {
            //return success with Api Resource
            return new SeriesResource(true, 'Data series Berhasil Disimpan!', $series);
        }

        //return failed with Api Resource
        return new SeriesResource(false, 'Data series Gagal Disimpan!', null);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $series = Series::whereId($id)->first();

        if($series) {
            //return success with Api Resource
            return new SeriesResource(true, 'Detail Data series!', $series);
        }

        //return failed with Api Resource
        return new SeriesResource(false, 'Detail Data series Tidak Ditemukan!', null);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Series $series)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|unique:series,name,'.$series->id,
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //update series
        $series->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
        ]);

        if($series) {
            //return success with Api Resource
            return new SeriesResource(true, 'Data series Berhasil Diupdate!', $series);
        }

        //return failed with Api Resource
        return new SeriesResource(false, 'Data series Gagal Diupdate!', null);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Series $series)
    {
        if($series->delete()) {
            //return success with Api Resource
            return new SeriesResource(true, 'Data series Berhasil Dihapus!', null);
        }

        //return failed with Api Resource
        return new SeriesResource(false, 'Data series Gagal Dihapus!', null);
    }
}


