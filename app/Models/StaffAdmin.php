<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffAdmin extends Model
{
    use HasFactory;

    public function nilais()
    {
        return $this->hasMany(Nilai::class);
    }
}


