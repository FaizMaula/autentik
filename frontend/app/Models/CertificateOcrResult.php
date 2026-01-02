<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateOcrResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'certificate_id',
        'ocr_engine',      // ✅ TAMBAHKAN INI
        'ocr_text',
        'ocr_details',
    ];

    protected $casts = [
        'ocr_details' => 'array',
    ];

    public function certificate()
    {
        return $this->belongsTo(Certificate::class);
    }
}

