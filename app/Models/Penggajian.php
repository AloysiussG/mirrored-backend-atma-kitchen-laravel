<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penggajian extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'karyawan_id',
        'total_gaji',
        'tanggal_gaji'
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }
}
