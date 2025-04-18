<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BukuController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\PeminjamanController;
use App\Http\Controllers\Api\PengembalianController;
use App\Http\Controllers\Api\LaporanController;
use Illuminate\Http\Request;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/token', function (Request $request) {
    $token = $request->session()->token();

    $token = csrf_token();

    return response()->json(['csrf_token' => $token]);
});
Route::get('/csrf-token', function (Request $request) {
    return response()->json(['csrf_token' => csrf_token()]);
});

Route::apiResource('buku', BukuController::class);
Route::apiResource('member', MemberController::class);
Route::post('pinjam', [PeminjamanController::class, 'store']);
Route::post('kembali', [PengembalianController::class, 'store']);
Route::get('laporan/pinjam', [LaporanController::class, 'rekapPinjam']);
Route::get('laporan/kembali', [LaporanController::class, 'detailKembali']);