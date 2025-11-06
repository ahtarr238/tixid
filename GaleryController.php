<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Galery;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class GaleryController
{
    public function index()
    {
        $galleries = Galery::orderBy('uploaded_at', 'desc')->get();
        return view('staff.galery.index', compact('galleries'));
    }

    public function create()
    {
        return view('staff.galery.create');
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|in:osis,mpk',
            'description' => 'required|string',
            'gambar' => 'required|mimes:jpeg,png,jpg,webp,svg',
        ]);

        // Upload gambar
        if ($request->hasFile('gambar')) {
            $gambar = $request->file('gambar');
            $namaGambar = Str::random(5) . "-gambar." . $gambar->getClientOriginalExtension();
            $path = $gambar->storeAs('galery', $namaGambar, 'public');

            if (!$path) {
                return redirect()->back()->withInput()->with('error', 'Gagal mengupload gambar!');
            }
        } else {
            return redirect()->back()->withInput()->with('error', 'Tidak ada gambar yang diupload!');
        }

        $gallery = new Galery();
        $gallery->title = $request->title;
        $gallery->category = strtolower($request->category);
        $gallery->description = $request->description;
        $gallery->photo_url = $path;
        $gallery->uploaded_by = auth()->user()->id;
        $gallery->uploaded_at = now();

        $saved = $gallery->save();

        if ($saved) {
            return redirect()->route('staff.galery.index')->with('success', 'Gallery berhasil ditambahkan!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan gallery!');
        }
    }

    public function edit($id)
    {
        $gallery = Galery::findOrFail($id);
        return view('staff.galery.edit', compact('gallery'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|in:osis,mpk',
            'description' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
        ]);

        $gallery = Galery::findOrFail($id);
        $gallery->title = $request->title;
        $gallery->category = strtolower($request->category);
        $gallery->description = $request->description;

        // Upload gambar baru jika ada
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama
            if ($gallery->photo_url) {
                Storage::disk('public')->delete('galery/' . $gallery->photo_url);
            }

            $gambar = $request->file('gambar');
            $namaGambar = Str::random(5) . "-gambar." . $gambar->getClientOriginalExtension();
            $path = $gambar->storeAs('galery', $namaGambar, 'public');

            if ($path) {
                $gallery->photo_url = $path;
            }
        }

        $gallery->save();

        return redirect()->route('staff.galery.index')->with('success', 'Gallery berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $gallery = Galery::findOrFail($id);

        // Hapus gambar dari storage
        if ($gallery->photo_url) {
            Storage::disk('public')->delete('galery/' . $gallery->photo_url);
        }

        $gallery->delete();

        return redirect()->route('staff.galery.index')->with('success', 'Gallery berhasil dihapus!');
    }
}
