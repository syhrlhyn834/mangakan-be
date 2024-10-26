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
            'image' => 'required|image|mimes:jpeg,jpg,png,webp,svg|max:2000',
            'manga_id' => 'required|exists:mangas,id', // Validasi untuk memastikan manga_id valid
            'title' => 'required|unique:chapters',
            'chapter_number' => 'required|numeric',
            'content' => 'required_without:url|file|mimes:pdf,cbz',
            'url' => 'required_without:content|url'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Upload image
        $image = $request->file('image');
        $image->storeAs('public/chapters', $image->hashName());

        // Cek jika `content` adalah file atau URL
        if ($request->hasFile('content')) {
            $content = $request->file('content');
            $content->storeAs('public/chapters_content', $content->hashName());
            $contentPath = $content->hashName(); // Simpan nama file
        } else {
            $contentPath = $request->url; // Simpan URL jika `content` berupa URL
        }

        // Buat chapter baru
        $chapter = Chapter::create([
            'image' => $image->hashName(),
            'manga_id' => $request->manga_id,
            'title' => $request->title,
            'slug' => Str::slug($request->title, '-'),
            'chapter_number' => $request->chapter_number,
            'content' => $contentPath, // Menyimpan file atau URL
        ]);

        return new ChapterResource(true, 'Data chapter Berhasil Disimpan!', $chapter);
    }

    public function show($id)
    {
        $chapter = Chapter::find($id);
        if (!$chapter) {
            return new ChapterResource(false, 'Data chapter Tidak Ditemukan', null);
        }
        return new ChapterResource(true, 'Detail Data Chapter', $chapter);
    }

    public function update(Request $request, Chapter $chapter)
    {

        $validator = Validator::make($request->all(), [
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp,svg|max:2000',
            'manga_id' => 'required|exists:mangas,id', // Validasi untuk memastikan manga_id valid
            'title' => 'required|unique:chapters,title',
            'chapter_number' => 'required|numeric',
            'content' => 'required_without:url|file|mimes:pdf,cbz',
            'url' => 'required_without:content|url'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Update image jika ada
        if ($request->file('image')) {
            Storage::disk('local')->delete('public/chapters/'.basename($chapter->image));
            $image = $request->file('image');
            $image->storeAs('public/chapters', $image->hashName());
            $chapter->image = $image->hashName();
        }

        // Update `content` jika ada file baru atau URL baru
        if ($request->hasFile('content')) {
            Storage::disk('local')->delete('public/chapters_content/'.basename($chapter->content));
            $content = $request->file('content');
            $content->storeAs('public/chapters_content', $content->hashName());
            $contentPath = $content->hashName();
        } elseif ($request->url) {
            $contentPath = $request->url;
        } else {
            $contentPath = $chapter->content; // Pertahankan konten lama jika tidak ada perubahan
        }

        // Update data chapter
        $chapter->update([
            'manga_id' => $request->manga_id,
            'title' => $request->title,
            'slug' => Str::slug($request->title, '-'),
            'chapter_number' => $request->chapter_number,
            'content' => $contentPath,
        ]);

        return new ChapterResource(true, 'Data chapter Berhasil Diupdate!', $chapter);
    }

    public function destroy(Chapter $chapter)
{
    Storage::disk('local')->delete('public/chapters/' . basename($chapter->image));
    Storage::disk('local')->delete('public/chapters_content/' . basename($chapter->content));
    if($chapter->delete()) {
        //return success with Api Resource
        return new ChapterResource(true, 'Data manga Berhasil Dihapus!', null);
    }

    //return failed with Api Resource
    return new ChapterResource(false, 'Data manga Gagal Dihapus!', null);
}

}
