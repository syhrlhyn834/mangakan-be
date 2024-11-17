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
        $chapters = Chapter::with('manga')->when(request()->q, function($chapters) {
            $chapters = $chapters->where('title', 'like', '%'. request()->q . '%');
        })->latest()->paginate(5);

        //return with Api Resource
        return new ChapterResource(true, 'List Data Chapter', $chapters);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp,svg|max:2000',
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
        $chapter = Chapter::with( 'manga')->whereId($id)->first();

        if($chapter) {
            //return success with Api Resource
            return new ChapterResource(true, 'Detail Data chapter!', $chapter);
        }

        //return failed with Api Resource
        return new ChapterResource(false, 'Detail Data chapter Tidak DItemukan!', null);
    }

    public function update(Request $request, Chapter $chapter)
{
    $validator = Validator::make($request->all(), [
        'image' => 'nullable|image|mimes:jpeg,jpg,png,webp,svg|max:2000',
        'manga_id' => 'required|exists:mangas,id',
        'title' => 'required|unique:chapters,title,' . $chapter->id,
        'chapter_number' => 'required|numeric',
        'content' => 'nullable|file|mimes:pdf,cbz',
        'url' => 'nullable|url',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    if ($request->hasFile('content')) {
        Storage::disk('local')->delete('public/chapters_content/'.basename($chapter->content));
        $content = $request->file('content');
        $content->storeAs('public/chapters_content', $content->hashName());
        $chapter->content = $content->hashName();
    } elseif ($request->url) {
        $chapter->content = $request->url;
    }

    if ($request->hasFile('image')) {
        Storage::disk('local')->delete('public/chapters/'.basename($chapter->image));
        $image = $request->file('image');
        $image->storeAs('public/chapters', $image->hashName());
        $chapter->image = $image->hashName();
    }

    $chapter->update([
        'manga_id' => $request->manga_id,
        'title' => $request->title,
        'slug' => Str::slug($request->title, '-'),
        'chapter_number' => $request->chapter_number,
        'content' => $chapter->content, // Tetap gunakan content yang sudah diset
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
