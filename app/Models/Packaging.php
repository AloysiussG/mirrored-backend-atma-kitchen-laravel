<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Packaging extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    public function hampers()
    {
        return $this->belongsTo(Hampers::class);
    }

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }

    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class);
    }
}
