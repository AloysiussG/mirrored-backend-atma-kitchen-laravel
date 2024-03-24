<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailResep extends Model
{
    use HasFactory;

    protected $fillable = [
        'resep_id',
        'bahan_baku_id',
        'jumlah_bahan_resep',
        'satuan_detail_resep'
    ];

    public function resep()
    {
        return $this->belongsTo(Resep::class);
    }

    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class);
    }
}
