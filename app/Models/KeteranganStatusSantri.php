<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeteranganStatusSantri extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'keterangan_status_santris';

    use HasFactory;

    public function statusSantris()
    {
        return $this->hasMany(StatusSantri::class);
    }

    public function statussantri()
    {
        return $this->hasOne(StatusSantri::class);
    }
}
