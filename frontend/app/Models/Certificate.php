<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'certificate_type',
        'nim',
        'internal_verified',
        'internal_verification_notes',
        'nama',
        'tahun_akademik',
        'penyelenggara',
        'tanggal_mulai',
        'tanggal_selesai',
        'nama_kegiatan',
        'nama_kegiatan_inggris',
        'berkas',
        'is_verified',
        'final_score',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'internal_verified' => 'boolean',
        'final_score' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ocrResults()
    {
        return $this->hasMany(CertificateOcrResult::class);
    }

        public function analysisResults()
    {
        return $this->hasMany(CertificateAnalysisResult::class);
    }
}
