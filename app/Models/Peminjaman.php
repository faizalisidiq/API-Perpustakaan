<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'tanggal_pinjam',
        'tanggal_kembali'
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function detail()
    {
        return $this->hasMany(DetailPeminjaman::class);
    }
}
