<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Qism extends Model
{
    use HasFactory;

    public function kelasSantris()
    {
        return $this->hasMany(KelasSantri::class);
    }

    public function mudirQism()
    {
        return $this->hasOne(MudirQism::class);
    }

    public function tahunAjaranAktifs()
    {
        return $this->hasMany(TahunAjaranAktif::class);
    }
}
