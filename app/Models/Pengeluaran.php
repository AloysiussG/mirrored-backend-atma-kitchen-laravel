<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'jenis_pengeluaran',
        'total_pengeluaran',
        'tanggal_pengeluaran'
    ];
}
