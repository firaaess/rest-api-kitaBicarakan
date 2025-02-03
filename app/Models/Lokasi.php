<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lokasi extends Model
{
    protected $fillable = ['nama'];
    public function pengaduan()
    {
        return $this->hasMany(Pengaduan::class);
    }
}
