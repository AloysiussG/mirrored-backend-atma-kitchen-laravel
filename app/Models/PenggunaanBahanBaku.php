<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenggunaanBahanBaku extends Model
{
    use HasFactory;

    protected $fillable = [
        'bahan_baku_id',
        'jumlah_penggunaan',
        'satuan_penggunaan',
        'tanggal_penggunaan'
    ];

    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class);
    }
}
