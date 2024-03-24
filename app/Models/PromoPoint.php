<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'jumlah_kelipatan_bayar',
        'jumlah_poin_diterima',
    ];
}
