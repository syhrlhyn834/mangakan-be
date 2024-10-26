<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\HeaderResource;
use App\Models\Header;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class HeaderController extends Controller
{
    public function index()
    {
        $headers = Header::latest()->paginate(5);
        return new HeaderResource(true, 'Data Header', $headers);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,jpg,png,webp,svg',
            'image2' => 'required|image|mimes:jpeg,jpg,png,webp,svg',
            'title' => 'required',
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $image = $request->file('image');
        $image->storeAs('public/headers', $image->hashName());

        $image2 = $request->file('image2');
        $image2->storeAs('public/headers', $image2->hashName());

        $header = Header::create([
            'image' => $image->hashName(),
            'image2' => $image2->hashName(),
            'title' => $request->title,
            'name' => $request->name,
        ]);

        return new HeaderResource(true, 'Data Header Berhasil Disimpan!', $header);
    }

    public function show($id)
    {
        $header = Header::find($id);
        if (!$header) {
            return new HeaderResource(false, 'Data header Tidak Ditemukan', null);
        }
        return new HeaderResource(true, 'Detail Data Soheadersmed', $header);
    }

    public function update(Request $request, $id)
    {
        $header = Header::find($id);
        if (!$header) {
            return new HeaderResource(false, 'Data Header Tidak Ditemukan', null);
        }

        $validator = Validator::make($request->all(), [
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp,svg',
            'image2' => 'nullable|image|mimes:jpeg,jpg,png,webp,svg',
            'title' => 'required',
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->file('image')) {
            Storage::disk('public')->delete('headers/' . $header->image);
            $image = $request->file('image');
            $image->storeAs('public/headers', $image->hashName());
            $header->image = $image->hashName();
        }

        if ($request->file('image2')) {
            Storage::disk('public')->delete('headers/' . $header->image2);
            $image2 = $request->file('image2');
            $image2->storeAs('public/headers', $image2->hashName());
            $header->image2 = $image2->hashName();
        }

        $header->update([
            'title' => $request->title,
            'name' => $request->name,
        ]);

        return new HeaderResource(true, 'Data Header Berhasil Diupdate!', $header);
    }

    public function destroy($id)
    {
        $header = Header::find($id);
        if (!$header) {
            return new HeaderResource(false, 'Data Header Tidak Ditemukan', null);
        }

        Storage::disk('public')->delete('headers/' . $header->image);
        Storage::disk('public')->delete('headers/' . $header->image2);

        $header->delete();

        return new HeaderResource(true, 'Data Header Berhasil Dihapus!', null);
    }
}
