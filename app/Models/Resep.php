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
        return $this->belongsTo(Produk::class);
    }
    
    public function detailResep()
    {
        return $this->hasMany(DetailResep::class);
    }
}
