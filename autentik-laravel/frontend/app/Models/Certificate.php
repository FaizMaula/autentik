<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'tahun_akademik',
        'penyelenggara',
        'tanggal_mulai',
        'tanggal_selesai',
        'nama_kegiatan',
        'nama_kegiatan_inggris',
        'berkas',
        'is_verified',
    ];
}
