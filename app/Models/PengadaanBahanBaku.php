<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengadaanBahanBaku extends Model
{
    use HasFactory;

    protected $fillable = [
        'bahan_baku_id',
        'jumlah_bahan',
        'harga_pengadaan_bahan_baku',
        'satuan_pengadaan',
        'tanggal_pengadaan',
    ];

    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class);
    }
}
