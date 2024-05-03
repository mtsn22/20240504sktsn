<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TahunBerjalan extends Model
{
    use HasFactory;

    public function kelassantri()
    {
        return $this->belongsTo(KelasSantri::class);
    }

    public function pesandaftar()
    {
        return $this->belongsTo(PesanDaftar::class);
    }
}
