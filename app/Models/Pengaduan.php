<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaduan extends Model
{
    protected $fillable = [
        'user_id', 'judul', 'isi_pengaduan', 'foto', 'status','kategori_id', 'lokasi_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function tanggapan()
    {
        return $this->hasMany(Tanggapan::class);
    }

    public function kategory()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');   
    }

    public function lokasi()
    {
        return $this->belongsTo(lokasi::class, 'lokasi_id');   
    }
}
