<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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

    /**
     * Get the status change logs for this certificate.
     */
    public function statusLogs()
    {
        return $this->hasMany(CertificateStatusLog::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get the latest status log.
     */
    public function latestStatusLog()
    {
        return $this->hasOne(CertificateStatusLog::class)->latestOfMany();
    }

    /**
     * Check if this certificate has been manually reviewed by admin.
     */
    public function wasManuallyReviewed(): bool
    {
        return $this->statusLogs()->exists();
    }

    /**
     * Get a temporary signed URL for viewing the certificate file.
     * URL expires after 60 minutes (1 hour).
     */
    public function getTemporaryFileUrl(int $expirationMinutes = 60): ?string
    {
        if (empty($this->berkas)) {
            return null;
        }

        try {
            return Storage::disk('r2')->temporaryUrl(
                $this->berkas,
                now()->addMinutes($expirationMinutes)
            );
        } catch (\Exception $e) {
            \Log::error('Failed to generate temporary URL for certificate: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get the file extension/type of the certificate.
     */
    public function getFileTypeAttribute(): ?string
    {
        if (empty($this->berkas)) {
            return null;
        }

        $extension = strtolower(pathinfo($this->berkas, PATHINFO_EXTENSION));
        
        return match ($extension) {
            'pdf' => 'pdf',
            'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp' => 'image',
            default => 'unknown',
        };
    }
    
    public function getOverallStatusAttribute(): string
    {
        // For internal certificates: use internal_verified
        if ($this->certificate_type === 'internal') {
            // Check if still processing (internal_verified is null)
            if (is_null($this->internal_verified)) {
                return 'pending';
            }
            return $this->internal_verified ? 'verified' : 'not_verified';
        }

        // For external certificates: check if still processing (final_score is -1)
        if ($this->final_score < 0) {
            return 'pending';
        }

        // Use final_score to determine status
        if ($this->final_score >= 75) {
            return 'verified';
        } elseif ($this->final_score >= 50) {
            return 'suspicious';
        }

        return 'not_verified';
    }

    /**
     * Get the human-readable status text.
     */
    public function getStatusTextAttribute(): string
    {
        $status = $this->overall_status;
        
        return match ($status) {
            'pending' => __('results.pending'),
            'verified' => __('results.verified'),
            'suspicious' => __('results.suspicious'),
            default => __('results.notVerified'),
        };
    }
}

