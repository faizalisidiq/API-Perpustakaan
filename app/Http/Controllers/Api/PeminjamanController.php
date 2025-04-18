<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\DetailPeminjaman;
use App\Models\Buku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeminjamanController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'tanggal_pinjam' => 'required|date',
            'buku' => 'required|array',
            'buku.*.id' => 'required|exists:bukus,id',
            'buku.*.jumlah' => 'required|integer|min:1'
        ]);

        DB::beginTransaction();

        try {
            $peminjaman = Peminjaman::create([
                'member_id' => $request->member_id,
                'tanggal_pinjam' => $request->tanggal_pinjam
            ]);

            foreach ($request->buku as $item) {
                $buku = Buku::findOrFail($item['id']);

                if ($buku->stok < $item['jumlah']) {
                    throw new \Exception("Stok buku '{$buku->judul}' tidak mencukupi.");
                }

                $buku->stok -= $item['jumlah'];
                $buku->save();

                DetailPeminjaman::create([
                    'peminjaman_id' => $peminjaman->id,
                    'buku_id' => $item['id'],
                    'jumlah' => $item['jumlah']
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'Peminjaman berhasil disimpan.'], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal: ' . $e->getMessage()], 400);
        }
    }
}