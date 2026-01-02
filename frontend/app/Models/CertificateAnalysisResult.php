<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateAnalysisResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'certificate_id',
        'google_results',
        'font_results',
        'match_scores',
        'verifikasi_ai',
        'analysis_version',
    ];

    protected $casts = [
        'google_results' => 'array',
        'font_results'   => 'array',
        'match_scores'   => 'array',
    ];

    public function certificate()
    {
        return $this->belongsTo(Certificate::class);
    }
}
