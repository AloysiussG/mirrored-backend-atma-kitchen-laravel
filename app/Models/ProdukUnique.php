<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdukUnique extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    public function produks()
    {
        return $this->hasMany(Produk::class);
    }

    public function resep()
    {
        return $this->hasOne(Resep::class, 'produk_id');
    }
}
