<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resep extends Model
{
    use HasFactory;

    protected $fillable = [
        'produk_id',
        'nama_resep',
    ];

    public function produk()
    {
        // udah diganti ke constrained 'produk_uniques' di migration bawah
        return $this->belongsTo(ProdukUnique::class);

        // return $this->belongsTo(Produk::class);
    }

    public function produkUnique()
    {
        return $this->belongsTo(ProdukUnique::class, 'produk_id', 'id');
    }

    public function detailResep()
    {
        return $this->hasMany(DetailResep::class);
    }
}
