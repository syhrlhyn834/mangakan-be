<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CharacterResource;
use App\Models\Character;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CharacterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //get characters
        $characters = Character::when(request()->q, function($characters) {
            $characters = $characters->where('name', 'like', '%'. request()->q . '%');
        })->latest()->paginate(5);

        //return with Api Resource
        return new CharacterResource(true, 'List Data characters', $characters);
    }

    public function characterView()
    {
        // Get genres with optional search query
        $genres = Character::when(request()->q, function($query) {
            return $query->where('name', 'like', '%'. request()->q . '%');
        })
        ->latest()  // Sort by the latest
        ->get();  // Get the results

        // Return response with API Resource
        return new CharacterResource(true, 'List Data characters', $genres);
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
            'name'     => 'required|unique:characters',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //create character
        $character = Character::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
        ]);

        if($character) {
            //return success with Api Resource
            return new CharacterResource(true, 'Data character Berhasil Disimpan!', $character);
        }

        //return failed with Api Resource
        return new CharacterResource(false, 'Data character Gagal Disimpan!', null);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $character = Character::whereId($id)->first();

        if($character) {
            //return success with Api Resource
            return new CharacterResource(true, 'Detail Data character!', $character);
        }

        //return failed with Api Resource
        return new CharacterResource(false, 'Detail Data character Tidak DItemukan!', null);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Character $character)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|unique:characters,name,'.$character->id,
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //update character
        $character->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
        ]);

        if($character) {
            //return success with Api Resource
            return new CharacterResource(true, 'Data character Berhasil Diupdate!', $character);
        }

        //return failed with Api Resource
        return new CharacterResource(false, 'Data character Gagal Diupdate!', null);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Character $character)
    {
        if($character->delete()) {
            //return success with Api Resource
            return new CharacterResource(true, 'Data character Berhasil Dihapus!', null);
        }

        //return failed with Api Resource
        return new CharacterResource(false, 'Data character Gagal Dihapus!', null);
    }
}



