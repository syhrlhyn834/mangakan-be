<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\TypeResource;
use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //get types
        $types = Type::when(request()->q, function($types) {
            $types = $types->where('name', 'like', '%'. request()->q . '%');
        })->latest()->paginate(5);

        //return with Api Resource
        return new TypeResource(true, 'List Data types', $types);
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
            'name'     => 'required|unique:types',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //create type
        $type = Type::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
        ]);

        if($type) {
            //return success with Api Resource
            return new TypeResource(true, 'Data type Berhasil Disimpan!', $type);
        }

        //return failed with Api Resource
        return new TypeResource(false, 'Data type Gagal Disimpan!', null);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $type = Type::whereId($id)->first();

        if($type) {
            //return success with Api Resource
            return new TypeResource(true, 'Detail Data type!', $type);
        }

        //return failed with Api Resource
        return new TypeResource(false, 'Detail Data type Tidak DItemukan!', null);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Type $type)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|unique:types,name,'.$type->id,
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //update type
        $type->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
        ]);

        if($type) {
            //return success with Api Resource
            return new TypeResource(true, 'Data type Berhasil Diupdate!', $type);
        }

        //return failed with Api Resource
        return new TypeResource(false, 'Data type Gagal Diupdate!', null);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Type $type)
    {
        if($type->delete()) {
            //return success with Api Resource
            return new TypeResource(true, 'Data type Berhasil Dihapus!', null);
        }

        //return failed with Api Resource
        return new TypeResource(false, 'Data type Gagal Dihapus!', null);
    }
}


