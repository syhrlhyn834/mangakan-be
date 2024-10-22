<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChapterResource;
use App\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ChapterController extends Controller
{
    public function index()
    {
        $chapters = Chapter::latest()->paginate(5);
        return new ChapterResource(true, 'Data chapter', $chapters);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,jpg,png,webp,svg',
            'manga_id'  => 'required',
            'title' => 'required|unique:chapters',
            'chapter_number' => 'required|numeric',
            'content' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $image = $request->file('image');
        $image->storeAs('public/chapters', $image->hashName());


        $chapter = chapter::create([
            'image' => $image->hashName(),
            'manga_id' => $request->manga_id,
            'title' => $request->title,
            'slug' => Str::slug($request->title, '-'),
            'chapter_number' => $request->chapter_number,
            'content' => $request->content,
        ]);

        return new ChapterResource(true, 'Data chapter Berhasil Disimpan!', $chapter);
    }

    public function show($id)
    {
        $chapter = Chapter::find($id);
        if (!$chapter) {
            return new ChapterResource(false, 'Data chapter Tidak Ditemukan', null);
        }
        return new ChapterResource(true, 'Detail Data Sochaptersmed', $chapter);
    }

    public function update(Request $request, $id)
    {
        $chapter = chapter::find($id);
        if (!$chapter) {
            return new ChapterResource(false, 'Data chapter Tidak Ditemukan', null);
        }

        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,jpg,png,webp,svg',
            'manga_id'  => 'required',
            'title' => 'required',
            'chapter_number' => 'required|numeric',
            'content' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->file('image')) {
            Storage::disk('public')->delete('chapters/' . $chapter->image);
            $image = $request->file('image');
            $image->storeAs('public/chapters', $image->hashName());
            $chapter->image = $image->hashName();
        }

        $chapter->update([
            'manga_id' => $request->manga_id,
            'title' => $request->title,
            'slug' => Str::slug($request->title, '-'),
            'chapter_number' => $request->chapter_number,
            'content' => $request->content,
        ]);

        return new chapterResource(true, 'Data chapter Berhasil Diupdate!', $chapter);
    }

    public function destroy($id)
    {
        $chapter = Chapter::find($id);
        if (!$chapter) {
            return new ChapterResource(false, 'Data chapter Tidak Ditemukan', null);
        }

        Storage::disk('public')->delete('chapters/' . $chapter->image);

        $chapter->delete();

        return new chapterResource(true, 'Data chapter Berhasil Dihapus!', null);
    }
}
