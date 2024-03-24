<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailCart extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    public function hampers()
    {
        return $this->belongsTo(Hampers::class);
    }
}
