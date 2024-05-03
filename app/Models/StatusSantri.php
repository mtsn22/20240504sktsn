<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusSantri extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'status_santris';

    use HasFactory;

    public function kss()
    {
        return $this->belongsTo(KeteranganStatusSantri::class);
    }

    public function santri()
    {
        return $this->belongsTo(Santri::class);
    }
}
