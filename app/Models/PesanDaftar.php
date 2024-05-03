<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PesanDaftar extends Model
{
    use HasFactory;

    public function walisantri()
    {
        return $this->belongsTo(Walisantri::class);
    }

    public function tahunberjalan()
    {
        return $this->hasOne(TahunBerjalan::class);
    }

}


