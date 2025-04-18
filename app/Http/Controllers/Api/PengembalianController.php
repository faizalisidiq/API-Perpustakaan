<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DetailPeminjaman;
use App\Models\Pengembalian;
use App\Models\Buku;
use Illuminate\Http\Request;

class PengembalianController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'detail_peminjaman_id' => 'required|exists:detail_peminjamen,id',
            'tanggal_pengembalian' => 'required|date'
        ]);

        $detail = DetailPeminjaman::findOrFail($request->detail_peminjaman_id);
        $buku = $detail->buku;

        Pengembalian::create([
            'detail_peminjaman_id' => $request->detail_peminjaman_id,
            'tanggal_pengembalian' => $request->tanggal_pengembalian
        ]);

        $buku->stok += $detail->jumlah;
        $buku->save();

        return response()->json(['message' => 'Pengembalian berhasil.']);
    }
}