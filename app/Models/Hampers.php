<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hampers extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    public function detailHampers()
    {
        return $this->hasMany(DetailHampers::class);
    }
}
