<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'karyawan_id',
        'tanggal_bolos'
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }
}
