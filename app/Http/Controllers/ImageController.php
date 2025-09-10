<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Image;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    /**
     * Tampilkan form upload
     */
    public function create()
    {
        return view('images.create');
    }

    /**
     * Proses upload gambar
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $filename = time() . '_' . $imageFile->getClientOriginalName();
            $path = $imageFile->storeAs('images', $filename, 'public');
            $image = new Image();
            $image->title = $request->title ?? $imageFile->getClientOriginalName();
            $image->filename = $filename;
            $image->path = $path;
            $image->save();
        }

        return back()->with('error', 'Gagal mengunggah gambar!');
    }

    /**
     * Tampilkan semua gambar
     */
    public function index()
    {
        $images = Image::latest()->paginate(10);
        return response()->json($images);
    }

    /**
     * Tampilkan gambar tertentu
     */
    public function show($id)
    {
        $image = Image::findOrFail($id);
        return view('images.show', compact('image'));
    }

    /**
     * Hapus gambar
     */
    public function destroy($id)
    {
        $image = Image::findOrFail($id);

        // Hapus file dari storage
        if (Storage::disk('public')->exists($image->path)) {
            Storage::disk('public')->delete($image->path);
        }

        // Hapus record dari database
        $image->delete();

        return redirect()->route('images.index')
            ->with('success', 'Gambar berhasil dihapus!');
    }
}
