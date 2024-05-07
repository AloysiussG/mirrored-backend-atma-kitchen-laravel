<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    public function statusTransaksi()
    {
        return $this->belongsTo(StatusTransaksi::class);
    }

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function alamat()
    {
        return $this->belongsTo(Alamat::class);
    }

    public function packagings()
    {
        return $this->hasMany(Packaging::class);
    }
}
