<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\GroupResource;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //get groups
        $groups = Group::when(request()->q, function($groups) {
            $groups = $groups->where('name', 'like', '%'. request()->q . '%');
        })->latest()->paginate(5);

        //return with Api Resource
        return new GroupResource(true, 'List Data groups', $groups);
    }

    public function groupView()
    {
        // Get genres with optional search query
        $genres = Group::when(request()->q, function($query) {
            return $query->where('name', 'like', '%'. request()->q . '%');
        })
        ->latest()  // Sort by the latest
        ->get();  // Get the results

        // Return response with API Resource
        return new GroupResource(true, 'List Data groups', $genres);
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
            'name'     => 'required|unique:groups',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //create group
        $group = Group::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
        ]);

        if($group) {
            //return success with Api Resource
            return new GroupResource(true, 'Data group Berhasil Disimpan!', $group);
        }

        //return failed with Api Resource
        return new GroupResource(false, 'Data group Gagal Disimpan!', null);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $group = Group::whereId($id)->first();

        if($group) {
            //return success with Api Resource
            return new GroupResource(true, 'Detail Data group!', $group);
        }

        //return failed with Api Resource
        return new GroupResource(false, 'Detail Data group Tidak DItemukan!', null);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Group $group)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|unique:groups,name,'.$group->id,
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //update group
        $group->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
        ]);

        if($group) {
            //return success with Api Resource
            return new GroupResource(true, 'Data group Berhasil Diupdate!', $group);
        }

        //return failed with Api Resource
        return new GroupResource(false, 'Data group Gagal Diupdate!', null);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Group $group)
    {
        if($group->delete()) {
            //return success with Api Resource
            return new GroupResource(true, 'Data group Berhasil Dihapus!', null);
        }

        //return failed with Api Resource
        return new GroupResource(false, 'Data group Gagal Dihapus!', null);
    }
}



