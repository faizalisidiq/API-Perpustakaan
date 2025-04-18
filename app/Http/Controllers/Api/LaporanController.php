<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function rekapPinjam()
    {
        $data = DB::table('peminjamen')
            ->join('members', 'members.id', '=', 'peminjamen.member_id')
            ->join('detail_peminjamen', 'peminjamen.id', '=', 'detail_peminjamen.peminjaman_id')
            ->join('bukus', 'bukus.id', '=', 'detail_peminjamen.buku_id')
            ->select(
                'members.nama as nama_member',
                'bukus.judul as judul_buku',
                'detail_peminjamen.jumlah',
                'peminjamen.tanggal_pinjam'
            )
            ->orderBy('peminjamen.tanggal_pinjam', 'desc')
            ->get();

        return response()->json($data);
    }

    public function detailKembali()
    {
        $data = DB::table('pengembalians')
            ->join('detail_peminjamen', 'pengembalians.peminjaman_id', '=', 'detail_peminjamen.id')
            ->join('bukus', 'bukus.id', '=', 'detail_peminjamen.buku_id')
            ->join('peminjamen', 'detail_peminjamen.peminjaman_id', '=', 'peminjamen.id')
            ->join('members', 'peminjamen.member_id', '=', 'members.id')
            ->select(
                'members.nama as nama_member',
                'bukus.judul as judul_buku',
                'detail_peminjamen.jumlah',
                'pengembalians.tanggal_kembali'
            )
            ->orderBy('pengembalians.tanggal_kembali', 'desc')
            ->get();

        return response()->json($data);
    }
}