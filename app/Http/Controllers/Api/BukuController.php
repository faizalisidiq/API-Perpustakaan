<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use App\Models\Buku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidatedInput;

class BukuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bukus = Buku::all();
        return response()->json($bukus);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'penulis' => 'required|string|max:255',
            'penerbit' => 'required|string|max:255',
            'tahun' => 'required|integer',
            'stok' => 'required|integer',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Proses upload foto jika ada
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->storeAs(
                'public/foto_buku',
                uniqid() . '.' . $request->file('foto')->getClientOriginalExtension()
            );
        }

        // Menyimpan buku baru
        $buku = Buku::create([
            'judul' => $validated['judul'],
            'penulis' => $validated['penulis'],
            'penerbit' => $validated['penerbit'],
            'tahun' => $validated['tahun'],
            'stok' => $validated['stok'],
            'foto' => $fotoPath ? Storage::url($fotoPath) : null,
        ]);

        return response()->json($buku, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $buku = Buku::find($id);

        if (!$buku) {
            return response()->json(['message' => 'Buku tidak ditemukan'], 404);
        }

        return response()->json($buku);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $buku = Buku::find($id);

        if (!$buku) {
            return response()->json(['message' => 'Buku tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'penulis' => 'required|string|max:255',
            'penerbit' => 'required|string|max:255',
            'tahun' => 'required|integer',
            'stok' => 'required|integer',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Proses upload foto jika ada
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($buku->foto) {
                Storage::delete(str_replace('storage', 'public', $buku->foto));
            }

            // Upload foto baru
            $fotoPath = $request->file('foto')->store('public/foto_buku');
            $buku->foto = Storage::url($fotoPath);
        }

        // Update data buku
        $buku->judul = $request->judul;
        $buku->penulis = $request->penulis;
        $buku->penerbit = $request->penerbit;
        $buku->tahun = $request->tahun;
        $buku->stok = $request->stok;
        $buku->save();

        return response()->json($buku);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $buku = Buku::find($id);

        if (!$buku) {
            return response()->json(['message' => 'Buku tidak ditemukan'], 404);
        }

        // Hapus foto buku jika ada
        if ($buku->foto) {
            Storage::delete(str_replace('storage', 'public', $buku->foto));
        }

        $buku->delete();

        return response()->json(['message' => 'Buku berhasil dihapus']);
    }
}