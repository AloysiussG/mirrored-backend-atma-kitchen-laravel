<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    // protected $fillable = [
    //     'kategori_produk_id',
    //     'penitip_id',
    //     'nama_produk',
    //     'jumlah_stock',
    //     'status',
    //     'harga',
    //     'kuota_harian',
    //     'foto_produk',
    // ];

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    public function kategoriProduk()
    {
        return $this->belongsTo(KategoriProduk::class);
    }

    public function penitip()
    {
        return $this->belongsTo(Penitip::class);
    }
}
